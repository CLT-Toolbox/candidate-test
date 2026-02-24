<?php

namespace App\Http\Controllers;

use App\Models\CltLayups;
use App\Models\CltLayers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CltLayupController extends Controller
{
    public function show($id) {
        try {
            $layup = CltLayups::findOrFail($id);
            return view('clt_layup_detail', compact('layup'));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Clt Layup: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to retrieve Clt Layup.');
        }
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|integer',
            'status' => 'required|integer',
            'species_grade' => 'nullable|string|max:255',
        ]);

        try {       
            $existing = CltLayups::where('supplier_id', $validated['supplier_id'])
                    ->where('name', $validated['name'])
                    ->first();

            if ($existing) {
                return redirect()
                    ->back()
                    ->with('error', 'Clt Layup already exists.');
            }
                     
            CltLayups::create($validated);

            return redirect()
                ->back()
                ->with('success', 'Clt Layup created successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create Clt Layup: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create Clt Layup.');
        }
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups')
                    ->where(fn ($query) =>
                        $query->where('supplier_id', $request->supplier_id)
                    )
                    ->ignore($id),
            ],
            'supplier_id' => 'required|integer',
            'status' => 'required|integer',
            'species_grade' => 'nullable|string|max:255',
        ]);        

        try {
            $layup = CltLayups::findOrFail($id);
            $layup->update($validated);

            return redirect()
                ->back()
                ->with('success', 'Clt Layup updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update Clt Layup: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update Clt Layup.');
        }
    }

    public function destroy($id){
        try {
            $layup = CltLayups::findOrFail($id);
            $layup->delete();

            return redirect()
                ->back()
                ->with('success', 'Clt Layup deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete Clt Layup: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to delete Clt Layup.');
        }
    }

    public function import(Request $request) {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,json|max:10240',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'conflict_strategy' => 'required|in:skip,review,update,overwrite,duplicate,reject',
        ]);

        try {
            $file = $request->file('file');
            $supplierId = $validated['supplier_id'];
            $conflictStrategy = $validated['conflict_strategy'];
            
            $fileContent = file_get_contents($file->getRealPath());
            $fileExtension = strtolower($file->getClientOriginalExtension());

            // Normalize structure
            if ($fileExtension === 'csv') {
                $rows = $this->parseCSV($fileContent);
                $parsedLayups = $this->groupParsedRowsIntoLayups($rows);
            } else {
                $decoded = json_decode($fileContent, true);
                if (!is_array($decoded)) {
                    $decoded = [$decoded];
                }
                // ensure layers key exists for each
                $parsedLayups = array_map(function ($l) {
                    if (!isset($l['layers']) || !is_array($l['layers'])) $l['layers'] = [];
                    return $l;
                }, $decoded);
            }

            if (empty($parsedLayups)) {
                return redirect()->back()->with('error', 'The file does not contain any valid data.');
            }

            // Detect existing conflicts
            $toCreate = [];
            $toUpdate = [];
            $conflicts = [];

            foreach ($parsedLayups as $incoming) {
                $name = trim($incoming['name'] ?? '');
                if (!$name) continue;

                $existing = CltLayups::where('supplier_id', $supplierId)
                    ->where('name', $name)
                    ->with('layers')
                    ->first();

                if ($existing) {
                    // According to rules: same-name under same supplier => candidate same layup (do not auto-create)
                    // Detect conflicts at layup level: top-level field differences OR layer-level conflicts
                    $topConflict = false;
                    if (($incoming['species_grade'] ?? null) !== $existing->species_grade) $topConflict = true;
                    if (isset($incoming['status']) && $incoming['status'] != $existing->status) $topConflict = true;

                    // Prepare existing layers map by layer_order
                    $existingByOrder = [];
                    foreach ($existing->layers as $lx) {
                        if ($lx->layer_order !== null) $existingByOrder[$lx->layer_order] = $lx;
                    }

                    $layerConflicts = [];
                    $incomingLayers = $incoming['layers'] ?? [];
                    foreach ($incomingLayers as $il) {
                        $order = $il['layer_order'] ?? null;
                        if ($order !== null && isset($existingByOrder[$order])) {
                            $ex = $existingByOrder[$order];
                            // Compare thickness,width,angle — if any differ -> conflict
                            $thDiff = (string)($ex->thickness ?? '') !== (string)($il['thickness'] ?? '');
                            $wDiff = (string)($ex->width ?? '') !== (string)($il['width'] ?? '');
                            $aDiff = (string)($ex->angle ?? '') !== (string)($il['angle'] ?? '');
                            if ($thDiff || $wDiff || $aDiff) {
                                $layerConflicts[] = [
                                    'order' => $order,
                                    'existing' => [
                                        'id' => $ex->id,
                                        'layer_order' => $ex->layer_order,
                                        'thickness' => $ex->thickness,
                                        'width' => $ex->width,
                                        'angle' => $ex->angle,
                                    ],
                                    'incoming' => [
                                        'layer_order' => $il['layer_order'] ?? null,
                                        'thickness' => $il['thickness'] ?? null,
                                        'width' => $il['width'] ?? null,
                                        'angle' => $il['angle'] ?? null,
                                    ],
                                ];
                            }
                        }
                    }

                    if ($topConflict || !empty($layerConflicts)) {
                        $conflicts[] = [
                            'name' => $name,
                            'existing' => [
                                'id' => $existing->id,
                                'name' => $existing->name,
                                'species_grade' => $existing->species_grade,
                                'status' => $existing->status,
                                'updated_at' => $existing->updated_at->toDateTimeString(),
                                'layers' => $existing->layers->map(function ($layer) {
                                    return [
                                        'id' => $layer->id,
                                        'layer_order' => $layer->layer_order,
                                        'thickness' => $layer->thickness,
                                        'width' => $layer->width,
                                        'angle' => $layer->angle,
                                    ];
                                })->toArray(),
                            ],
                            'incoming' => [
                                'name' => $incoming['name'] ?? null,
                                'species_grade' => $incoming['species_grade'] ?? null,
                                'status' => $incoming['status'] ?? 1,
                                'layers' => $incoming['layers'] ?? [],
                            ],
                            'layer_conflicts' => $layerConflicts,
                        ];
                    } else {
                        // No conflicts detected per rules — mark as updatable
                        $toUpdate[] = [
                            'existing' => [
                                'id' => $existing->id,
                                'name' => $existing->name,
                                'species_grade' => $existing->species_grade,
                                'status' => $existing->status,
                                'updated_at' => $existing->updated_at->toDateTimeString(),
                                'layers' => $existing->layers->map(function ($layer) {
                                    return [
                                        'id' => $layer->id,
                                        'layer_order' => $layer->layer_order,
                                        'thickness' => $layer->thickness,
                                        'width' => $layer->width,
                                        'angle' => $layer->angle,
                                    ];
                                })->toArray(),
                            ],
                            'incoming' => [
                                'name' => $incoming['name'] ?? null,
                                'species_grade' => $incoming['species_grade'] ?? null,
                                'status' => $incoming['status'] ?? 1,
                                'layers' => $incoming['layers'] ?? [],
                            ],
                        ];
                    }
                } else {
                    $toCreate[] = [
                        'name' => $name,
                        'species_grade' => $incoming['species_grade'] ?? null,
                        'status' => $incoming['status'] ?? 1,
                        'layers' => $incoming['layers'] ?? [],
                    ];
                }
            }

            // Handle non-review strategies immediately
            if ($conflictStrategy !== 'review') {
                if ($conflictStrategy === 'reject') {
                    if (!empty($conflicts)) {
                        return redirect()->back()->with('error', 'Import rejected: conflicts detected.');
                    }
                }

                $successCount = 0;
                // Create non-conflicting new layups
                foreach ($toCreate as $c) {
                    try {
                        $new = CltLayups::create([
                            'name' => $c['name'],
                            'supplier_id' => $supplierId,
                            'species_grade' => $c['species_grade'],
                            'status' => $c['status'],
                        ]);
                        foreach ($c['layers'] as $layer) {
                            CltLayers::create(array_merge($layer, ['layup_id' => $new->id]));
                        }
                        $successCount++;
                    } catch (\Exception $e) {
                        Log::error('Error creating layup during import: ' . $e->getMessage());
                    }
                }

                // Handle existing layups that had no per-layer conflicts
                if (!empty($toUpdate)) {
                    foreach ($toUpdate as $u) {
                        try {
                            // $toUpdate items were constructed with an 'existing' snapshot (array),
                            // not 'existing_id'. Support both structures for robustness.
                            $existingId = $u['existing']['id'] ?? ($u['existing_id'] ?? null);
                            $incoming = $u['incoming'];
                            if ($conflictStrategy === 'skip') {
                                continue;
                            }

                            if ($conflictStrategy === 'overwrite' || $conflictStrategy === 'update') {
                                $layup = CltLayups::find($existingId);
                                if ($layup) {
                                    $layup->update([
                                        'species_grade' => $incoming['species_grade'] ?? $layup->species_grade,
                                        'status' => $incoming['status'] ?? $layup->status,
                                    ]);
                                    // Replace layers: delete existing and insert incoming
                                    CltLayers::where('layup_id', $layup->id)->delete();
                                    foreach ($incoming['layers'] as $layer) {
                                        CltLayers::create(array_merge($layer, ['layup_id' => $layup->id]));
                                    }
                                }
                            } elseif ($conflictStrategy === 'duplicate') {
                                $newName = ($u['incoming']['name'] ?? 'Unnamed') . ' (imported)';
                                $new = CltLayups::create([
                                    'name' => $newName,
                                    'supplier_id' => $supplierId,
                                    'species_grade' => $incoming['species_grade'] ?? null,
                                    'status' => $incoming['status'] ?? 1,
                                ]);
                                foreach ($incoming['layers'] as $layer) {
                                    CltLayers::create(array_merge($layer, ['layup_id' => $new->id]));
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('Error applying toUpdate during import: ' . $e->getMessage());
                        }
                    }
                }

                // Resolve conflicts according to strategy
                foreach ($conflicts as $conf) {
                    $name = $conf['name'];
                    $existingId = $conf['existing']['id'];
                    $incoming = $conf['incoming'];

                    try {
                        if ($conflictStrategy === 'skip') {
                            continue;
                        }

                        if ($conflictStrategy === 'overwrite' || $conflictStrategy === 'update') {
                            $layup = CltLayups::find($existingId);
                            if ($layup) {
                                $layup->update([
                                    'species_grade' => $incoming['species_grade'] ?? $layup->species_grade,
                                    'status' => $incoming['status'] ?? $layup->status,
                                ]);
                                // Replace layers: delete existing and insert incoming
                                CltLayers::where('layup_id', $layup->id)->delete();
                                foreach ($incoming['layers'] as $layer) {
                                    CltLayers::create(array_merge($layer, ['layup_id' => $layup->id]));
                                }
                            }
                        } elseif ($conflictStrategy === 'duplicate') {
                            $newName = $name . ' (imported)';
                            $new = CltLayups::create([
                                'name' => $newName,
                                'supplier_id' => $supplierId,
                                'species_grade' => $incoming['species_grade'] ?? null,
                                'status' => $incoming['status'] ?? 1,
                            ]);
                            foreach ($incoming['layers'] as $layer) {
                                CltLayers::create(array_merge($layer, ['layup_id' => $new->id]));
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error resolving conflict during import: ' . $e->getMessage());
                    }
                }

                $message = "Successfully processed import.";
                return redirect()->back()->with('success', $message);
            }

            // If review mode, return JSON describing conflicts, toCreate and toUpdate
            return response()->json([
                'conflicts' => $conflicts,
                'to_create' => $toCreate,
                'to_update' => $toUpdate,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to import layups: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to import file: ' . $e->getMessage());
        }
    }

    private function parseCSV($content) {
        $lines = array_filter(array_map('str_getcsv', explode("\n", $content)));
        
        if (empty($lines)) {
            return [];
        }

        $headers = array_shift($lines);
        $header_map = array_flip($headers);
        
        $data = [];
        foreach ($lines as $line) {
            if (count($line) > 0 && !empty($line[0])) {
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[trim(strtolower($header))] = $line[$index] ?? null;
                }
                $data[] = $row;
            }
        }

        return $data;
    }

    private function groupParsedRowsIntoLayups(array $rows)
    {
        $groups = [];
        foreach ($rows as $r) {
            $name = trim($r['name'] ?? '');
            if ($name === '') continue;
            if (!isset($groups[$name])) {
                $groups[$name] = [
                    'name' => $name,
                    'species_grade' => $r['species_grade'] ?? null,
                    'status' => isset($r['status']) ? (int)$r['status'] : 1,
                    'layers' => [],
                ];
            }

            // If row contains layer fields, attach as layer
            if (isset($r['layer_order']) || isset($r['thickness'])) {
                $groups[$name]['layers'][] = [
                    'layer_order' => isset($r['layer_order']) ? (int)$r['layer_order'] : null,
                    'thickness' => isset($r['thickness']) ? (float)$r['thickness'] : null,
                    'width' => isset($r['width']) ? (float)$r['width'] : null,
                    'angle' => isset($r['angle']) ? (float)$r['angle'] : null,
                ];
            }
        }

        return array_values($groups);
    }

    public function resolveImport(Request $request)
    {
        $payload = $request->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'decisions' => 'required|array',
        ]);

        $supplierId = $payload['supplier_id'];
        $decisions = $payload['decisions'];

        try {
            foreach ($decisions as $d) {
                $action = $d['action'] ?? null; // keep|accept|duplicate
                $incoming = $d['incoming'] ?? null;
                $existingId = $d['existing_id'] ?? null;

                if ($action === 'keep') {
                    continue;
                }

                if ($action === 'accept' && $existingId && $incoming) {
                    $layup = CltLayups::find($existingId);
                    if ($layup) {
                        $layup->update([
                            'species_grade' => $incoming['species_grade'] ?? $layup->species_grade,
                            'status' => $incoming['status'] ?? $layup->status,
                        ]);
                        CltLayers::where('layup_id', $layup->id)->delete();
                        foreach ($incoming['layers'] as $layer) {
                            CltLayers::create(array_merge($layer, ['layup_id' => $layup->id]));
                        }
                    }
                }

                if ($action === 'duplicate' && $incoming) {
                    $newName = ($incoming['name'] ?? 'Unnamed') . ' (imported)';
                    $new = CltLayups::create([
                        'name' => $newName,
                        'supplier_id' => $supplierId,
                        'species_grade' => $incoming['species_grade'] ?? null,
                        'status' => $incoming['status'] ?? 1,
                    ]);
                    foreach ($incoming['layers'] as $layer) {
                        CltLayers::create(array_merge($layer, ['layup_id' => $new->id]));
                    }
                }

                if ($action === 'create' && $incoming) {
                    // used for non-conflicting layups
                    $new = CltLayups::create([
                        'name' => $incoming['name'] ?? 'Unnamed',
                        'supplier_id' => $supplierId,
                        'species_grade' => $incoming['species_grade'] ?? null,
                        'status' => $incoming['status'] ?? 1,
                    ]);
                    foreach ($incoming['layers'] as $layer) {
                        CltLayers::create(array_merge($layer, ['layup_id' => $new->id]));
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to apply resolved import: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function export(Request $request, $supplierId)
    {
        $format = strtolower($request->query('format', 'csv'));

        if (!in_array($format, ['csv', 'json'])) {
            return redirect()->back()->with('error', 'Invalid export format.');
        }

        try {
            $layups = CltLayups::where('supplier_id', $supplierId)
                ->with('layers')
                ->get();

            $fileName = 'layups_supplier_' . $layups[0]->supplier->name . '.' . $format;

            if ($format === 'json') {
                $payload = $layups->map(function ($l) {
                    return [
                        'id' => $l->id,
                        'name' => $l->name,
                        'species_grade' => $l->species_grade,
                        'status' => $l->status,
                        'updated_at' => $l->updated_at->toDateTimeString(),
                        'layers' => $l->layers->map(function ($layer) {
                            return [
                                'id' => $layer->id,
                                'layer_order' => $layer->layer_order,
                                'thickness' => $layer->thickness,
                                'width' => $layer->width,
                                'angle' => $layer->angle,
                            ];
                        })->toArray(),
                    ];
                })->toArray();

                return response(json_encode($payload, JSON_PRETTY_PRINT), 200, [
                    'Content-Type' => 'application/json',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            // CSV format
            $headers = ['layup_id', 'layup_name', 'species_grade', 'layup_status', 'layup_updated_at', 'layer_id', 'layer_order', 'thickness', 'width', 'angle'];

            $fp = fopen('php://temp', 'r+');
            fputcsv($fp, $headers);

            foreach ($layups as $l) {
                if ($l->layers && $l->layers->count() > 0) {
                    foreach ($l->layers as $layer) {
                        fputcsv($fp, [
                            $l->id,
                            $l->name,
                            $l->species_grade,
                            $l->status,
                            $l->updated_at->toDateTimeString(),
                            $layer->id,
                            $layer->layer_order,
                            $layer->thickness,
                            $layer->width,
                            $layer->angle,
                        ]);
                    }
                } else {
                    fputcsv($fp, [
                        $l->id,
                        $l->name,
                        $l->species_grade,
                        $l->status,
                        $l->updated_at->toDateTimeString(),
                        '', '', '', '', '',
                    ]);
                }
            }

            rewind($fp);
            $csv = stream_get_contents($fp);
            fclose($fp);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export layups.');
        }
    }
}
