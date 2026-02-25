<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    /**
     * Process the import file and detect conflicts.
     */
    public function process(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json,csv,xlsx,xls|max:10240',
            'conflict_strategy' => 'required|in:skip,overwrite,manual',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $strategy = $request->conflict_strategy;

        // Parse the file
        try {
            $importData = $this->parseFile($file);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse file: ' . $e->getMessage(),
            ], 400);
        }

        if (!$importData || empty($importData)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse file. Please check the file format.',
            ], 400);
        }

        // Detect conflicts
        $conflicts = $this->detectConflicts($supplier, $importData);

        // If manual resolution and conflicts exist, return conflicts for UI
        if ($strategy === 'manual' && count($conflicts) > 0) {
            return response()->json([
                'success' => true,
                'has_conflicts' => true,
                'conflicts' => $conflicts,
                'strategy' => $strategy,
            ]);
        }

        // Otherwise, process the import with the chosen strategy
        $result = $this->executeImport($supplier, $importData, $conflicts, $strategy);
        return response()->json([
            'success' => true,
            'has_conflicts' => false,
            'result' => $result,
        ]);
    }

    /**
     * Confirm import after manual conflict resolution.
     */
    public function confirm(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'resolutions' => 'required|array',
            'import_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $resolutions = $request->resolutions;
        $importData = $request->import_data;

        $result = $this->executeImportWithResolutions($supplier, $importData, $resolutions);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }

    /**
     * Parse the uploaded file.
     */
    private function parseFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getRealPath());

        if ($extension === 'json') {
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
            }
            return $data;
        } elseif ($extension === 'csv') {
            return $this->parseCsv($file->getRealPath());
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            throw new \Exception('Excel import not yet supported. Please use JSON or CSV format.');
        }

        throw new \Exception('Unsupported file format: ' . $extension);
    }

    /**
     * Parse CSV file.
     */
    private function parseCsv($filePath)
    {
        $data = [];
        $currentLayup = null;

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $rowData = array_combine($headers, $row);

                // Handle both 'name' and 'layup_name' headers
                $layupName = $rowData['name'] ?? $rowData['layup_name'] ?? null;
                $layerOrder = $rowData['layer_order'] ?? null;

                // If layup_name exists and is different from current, start new layup
                if (!empty($layupName) && ($currentLayup === null || $currentLayup['name'] !== $layupName)) {
                    if ($currentLayup) {
                        $data[] = $currentLayup;
                    }
                    $currentLayup = [
                        'name' => $layupName,
                        'layers' => [],
                    ];
                }

                // Add layer to current layup
                if ($currentLayup && !empty($layerOrder)) {
                    $currentLayup['layers'][] = [
                        'layer_order' => (int) $layerOrder,
                        'thickness' => (float) ($rowData['thickness'] ?? 0),
                        'width' => (float) ($rowData['width'] ?? 0),
                        'angle' => (float) ($rowData['angle'] ?? 0),
                    ];
                }
            }

            if ($currentLayup) {
                $data[] = $currentLayup;
            }

            fclose($handle);
        }

        return $data;
    }

    /**
     * Parse Excel file.
     */
    private function parseExcel($filePath)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $data = [];
            $currentLayup = null;
            $firstRow = true;
            $headers = [];

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cell = $worksheet->getCell($col . $row);
                    $rowData[] = $cell->getValue();
                }

                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }

                // First row is headers
                if ($firstRow) {
                    $headers = $rowData;
                    $firstRow = false;
                    continue;
                }

                // Combine with headers
                $rowData = array_combine($headers, $rowData);

                // Handle both 'name' and 'layup_name' headers
                $layupName = $rowData['name'] ?? $rowData['layup_name'] ?? null;
                $layerOrder = $rowData['layer_order'] ?? null;

                // If layup_name exists and is different from current, start new layup
                if (!empty($layupName) && ($currentLayup === null || $currentLayup['name'] !== $layupName)) {
                    if ($currentLayup) {
                        $data[] = $currentLayup;
                    }
                    $currentLayup = [
                        'name' => $layupName,
                        'layers' => [],
                    ];
                }

                // Add layer to current layup
                if ($currentLayup && !empty($layerOrder)) {
                    $currentLayup['layers'][] = [
                        'layer_order' => (int) $layerOrder,
                        'thickness' => (float) ($rowData['thickness'] ?? 0),
                        'width' => (float) ($rowData['width'] ?? 0),
                        'angle' => (float) ($rowData['angle'] ?? 0),
                    ];
                }
            }

            if ($currentLayup) {
                $data[] = $currentLayup;
            }

            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Failed to parse Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Detect conflicts between import data and existing data.
     */
    private function detectConflicts(Supplier $supplier, array $importData)
    {
        $conflicts = [];

        foreach ($importData as $importLayup) {
            // Find existing layup with same name
            $existingLayup = CltLayup::where('supplier_id', $supplier->id)
                ->where('name', $importLayup['name'])
                ->with('layers')
                ->first();

            if ($existingLayup) {
                $layerConflicts = $this->compareLayups($existingLayup, $importLayup);

                if (count($layerConflicts) > 0) {
                    $conflicts[] = [
                        'id' => $existingLayup->id,
                        'name' => $importLayup['name'],
                        'conflictType' => 'Layer mismatch',
                        'existing' => [
                            'layers' => $existingLayup->layers->map(fn($l) => [
                                'order' => $l->layer_order,
                                'thickness' => $l->thickness,
                                'width' => $l->width,
                                'angle' => $l->angle,
                            ])->toArray(),
                        ],
                        'importing' => [
                            'layers' => $importLayup['layers'],
                        ],
                        'layerConflicts' => $layerConflicts,
                        'resolved' => false,
                        'resolution' => null,
                    ];
                }
            }
        }

        return $conflicts;
    }

    /**
     * Compare existing and importing layup layers.
     */
    private function compareLayups(CltLayup $existingLayup, array $importLayup)
    {
        $conflicts = [];
        $existingLayers = $existingLayup->layers->keyBy('layer_order');

        foreach ($importLayup['layers'] as $importLayer) {
            $order = $importLayer['layer_order'];

            if (isset($existingLayers[$order])) {
                $existing = $existingLayers[$order];
                $diffFields = [];

                if ((float) $existing->thickness != (float) $importLayer['thickness']) {
                    $diffFields[] = 'thickness';
                }
                if ((float) $existing->width != (float) $importLayer['width']) {
                    $diffFields[] = 'width';
                }
                if ((float) $existing->angle != (float) $importLayer['angle']) {
                    $diffFields[] = 'angle';
                }

                if (count($diffFields) > 0) {
                    $conflicts[] = [
                        'order' => $order,
                        'fields' => $diffFields,
                    ];
                }
            }
        }

        return $conflicts;
    }

    /**
     * Execute import with the chosen strategy.
     */
    private function executeImport(Supplier $supplier, array $importData, array $conflicts, string $strategy)
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($importData as $importLayup) {
                $existingLayup = CltLayup::where('supplier_id', $supplier->id)
                    ->where('name', $importLayup['name'])
                    ->first();

                $hasConflict = collect($conflicts)->contains('name', $importLayup['name']);

                if ($existingLayup) {
                    if ($hasConflict && $strategy === 'skip') {
                        $skipped++;
                        continue;
                    }

                    if ($strategy === 'overwrite' || !$hasConflict) {
                        // Delete existing layers
                        CltLayer::where('layup_id', $existingLayup->id)->delete();

                        // Create new layers
                        foreach ($importLayup['layers'] as $layer) {
                            CltLayer::create([
                                'layup_id' => $existingLayup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness' => $layer['thickness'],
                                'width' => $layer['width'],
                                'angle' => $layer['angle'],
                            ]);
                        }
                        $updated++;
                    }
                } else {
                    // Create new layup
                    $layup = CltLayup::create([
                        'supplier_id' => $supplier->id,
                        'name' => $importLayup['name'],
                    ]);

                    // Create layers
                    foreach ($importLayup['layers'] as $layer) {
                        CltLayer::create([
                            'layup_id' => $layup->id,
                            'layer_order' => $layer['layer_order'],
                            'thickness' => $layer['thickness'],
                            'width' => $layer['width'],
                            'angle' => $layer['angle'],
                        ]);
                    }
                    $created++;
                }
            }

            DB::commit();

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Execute import with manual resolutions.
     */
    private function executeImportWithResolutions(Supplier $supplier, array $importData, array $resolutions)
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            foreach ($importData as $importLayup) {
                $existingLayup = CltLayup::where('supplier_id', $supplier->id)
                    ->where('name', $importLayup['name'])
                    ->first();

                $resolution = collect($resolutions)->firstWhere('name', $importLayup['name']);

                if ($existingLayup && $resolution) {
                    if ($resolution['action'] === 'keep') {
                        $skipped++;
                        continue;
                    } elseif ($resolution['action'] === 'accept') {
                        // Delete existing layers
                        CltLayer::where('layup_id', $existingLayup->id)->delete();

                        // Create new layers
                        foreach ($importLayup['layers'] as $layer) {
                            CltLayer::create([
                                'layup_id' => $existingLayup->id,
                                'layer_order' => $layer['layer_order'],
                                'thickness' => $layer['thickness'],
                                'width' => $layer['width'],
                                'angle' => $layer['angle'],
                            ]);
                        }
                        $updated++;
                    }
                } elseif (!$existingLayup) {
                    // Create new layup
                    $layup = CltLayup::create([
                        'supplier_id' => $supplier->id,
                        'name' => $importLayup['name'],
                    ]);

                    // Create layers
                    foreach ($importLayup['layers'] as $layer) {
                        CltLayer::create([
                            'layup_id' => $layup->id,
                            'layer_order' => $layer['layer_order'],
                            'thickness' => $layer['thickness'],
                            'width' => $layer['width'],
                            'angle' => $layer['angle'],
                        ]);
                    }
                    $created++;
                }
            }

            DB::commit();

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
