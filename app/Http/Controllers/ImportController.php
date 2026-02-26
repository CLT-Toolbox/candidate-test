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
            $parseResult = $this->parseFile($file);
            $importData = $parseResult['data'];
            $exportedSupplierName = $parseResult['supplier_name'] ?? null;
            $exportedSupplierId = $parseResult['supplier_id'] ?? null;
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

        // Validate supplier match if file contains supplier info
        if ($exportedSupplierId && $exportedSupplierId != $supplier->id) {
            return response()->json([
                'success' => false,
                'message' => "This file was exported from supplier ID {$exportedSupplierId} ({$exportedSupplierName}). It can only be imported back to the same supplier.",
            ], 400);
        }

        // If supplier name exists but ID doesn't, check by name
        if (!$exportedSupplierId && $exportedSupplierName && $exportedSupplierName !== $supplier->name) {
            return response()->json([
                'success' => false,
                'message' => "This file was exported from supplier ID {$exportedSupplierId} ({$exportedSupplierName}). It can only be imported back to the same supplier.",
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

        try {
            $resolutions = $request->resolutions;
            $importData = $request->import_data;

            $result = $this->executeImportWithResolutions($supplier, $importData, $resolutions);

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import confirmation failed: ' . $e->getMessage(),
            ], 400);
        }
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

            // Check if it's our export format (with supplier_name and layups array)
            if (isset($data['layups']) && is_array($data['layups'])) {
                return [
                    'data' => $data['layups'],
                    'supplier_id' => $data['supplier_id'] ?? null,
                    'supplier_name' => $data['supplier_name'] ?? null,
                ];
            }

            // Otherwise assume it's an array of layups directly (no supplier info)
            return [
                'data' => $data,
                'supplier_id' => null,
                'supplier_name' => null,
            ];
        } elseif ($extension === 'csv') {
            return $this->parseCsv($file->getRealPath());
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseExcel($file->getRealPath());
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
        $supplierName = null;
        $supplierId = null;

        if (($handle = fopen($filePath, 'r')) !== false) {
            // First row is headers
            $headers = fgetcsv($handle);

            if (!$headers) {
                fclose($handle);
                return [
                    'data' => [],
                    'supplier_id' => null,
                    'supplier_name' => null,
                ];
            }

            // Clean up headers (trim whitespace)
            $headers = array_map('trim', $headers);
            // Filter out empty header values
            $headerIndices = [];
            foreach ($headers as $idx => $header) {
                if (!empty($header)) {
                    $headerIndices[$idx] = $header;
                }
            }

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Extract only columns that have headers
                $rowData = [];
                foreach ($headerIndices as $idx => $header) {
                    $rowData[$header] = $row[$idx] ?? null;
                }

                // Extract supplier info (from first data row)
                if (!$supplierId && isset($rowData['supplier_id']) && !empty($rowData['supplier_id'])) {
                    $supplierId = $rowData['supplier_id'];
                }
                if (!$supplierName && isset($rowData['supplier_name']) && !empty($rowData['supplier_name'])) {
                    $supplierName = $rowData['supplier_name'];
                }

                // Handle both 'name' and 'layup_name' headers
                $layupName = $rowData['layup_name'] ?? $rowData['name'] ?? null;
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

        return [
            'data' => $data,
            'supplier_id' => $supplierId ? (int) $supplierId : null,
            'supplier_name' => $supplierName,
        ];
    }

    /**
     * Parse Excel file.
     */
    private function parseExcel($filePath)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

            // Check if there's a metadata sheet
            $supplierName = null;
            $supplierId = null;

            if ($spreadsheet->getSheetCount() > 1) {
                try {
                    $metaSheet = $spreadsheet->getSheetByName('Export Info');
                    if ($metaSheet) {
                        $supplierId = $metaSheet->getCell('B1')->getValue();
                        $supplierName = $metaSheet->getCell('B2')->getValue();
                    }
                } catch (\Exception $e) {
                    // No metadata sheet, continue without it
                }
            }

            // Get data from first sheet
            $worksheet = $spreadsheet->getSheet(0);

            $data = [];
            $currentLayup = null;
            $firstRow = true;
            $headerMap = [];

            $highestRow = $worksheet->getHighestRow();

            for ($row = 1; $row <= $highestRow; $row++) {
                // First row is headers
                if ($firstRow) {
                    // Build header mapping
                    $colIndex = 0;
                    foreach ($worksheet->getRowIterator($row, $row)->current()->getCellIterator() as $cell) {
                        $headerValue = strtolower(trim($cell->getValue()));

                        // Map headers to our keys
                        if (strpos($headerValue, 'supplier') !== false) {
                            $headerMap[$colIndex] = 'supplier_name';
                        } elseif (strpos($headerValue, 'layup') !== false) {
                            $headerMap[$colIndex] = 'layup_name';
                        } elseif (strpos($headerValue, 'order') !== false) {
                            $headerMap[$colIndex] = 'layer_order';
                        } elseif (strpos($headerValue, 'thickness') !== false) {
                            $headerMap[$colIndex] = 'thickness';
                        } elseif (strpos($headerValue, 'width') !== false) {
                            $headerMap[$colIndex] = 'width';
                        } elseif (strpos($headerValue, 'angle') !== false) {
                            $headerMap[$colIndex] = 'angle';
                        }
                        $colIndex++;
                    }
                    $firstRow = false;
                    continue;
                }

                // Read data row
                $rowData = [];
                $colIndex = 0;
                $isEmpty = true;

                foreach ($worksheet->getRowIterator($row, $row)->current()->getCellIterator() as $cell) {
                    $value = $cell->getValue();
                    if (!empty($value)) {
                        $isEmpty = false;
                    }

                    if (isset($headerMap[$colIndex])) {
                        $key = $headerMap[$colIndex];
                        $rowData[$key] = $value;
                    }
                    $colIndex++;
                }

                // Skip empty rows
                if ($isEmpty) {
                    continue;
                }

                // Extract supplier name from data if not from metadata
                if (!$supplierName && isset($rowData['supplier_name']) && !empty($rowData['supplier_name'])) {
                    $supplierName = $rowData['supplier_name'];
                }

                // Get layup name and layer order
                $layupName = $rowData['layup_name'] ?? null;
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

            return [
                'data' => $data,
                'supplier_id' => $supplierId ? (int) $supplierId : null,
                'supplier_name' => $supplierName,
            ];
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
                                'thickness' => (float) $l->thickness,
                                'width' => (float) $l->width,
                                'angle' => (float) $l->angle,
                            ])->toArray(),
                        ],
                        'importing' => [
                            'layers' => array_map(function($layer) {
                                return [
                                    'order' => $layer['layer_order'],
                                    'thickness' => (float) $layer['thickness'],
                                    'width' => (float) $layer['width'],
                                    'angle' => (float) $layer['angle'],
                                ];
                            }, $importLayup['layers']),
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

                if (abs((float) $existing->thickness - (float) $importLayer['thickness']) > 0.01) {
                    $diffFields[] = 'thickness';
                }
                if (abs((float) $existing->width - (float) $importLayer['width']) > 0.01) {
                    $diffFields[] = 'width';
                }
                if (abs((float) $existing->angle - (float) $importLayer['angle']) > 0.01) {
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
                                'layer_order' => $layer['layer_order'] ?? $layer['order'] ?? 0,
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
                            'layer_order' => $layer['layer_order'] ?? $layer['order'] ?? 0,
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
