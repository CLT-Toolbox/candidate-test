<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        return Supplier::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        return Supplier::create($request->only('name'));
    }

    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $supplier->update($request->only('name'));

        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->noContent();
    }

    // IMPORT
    public function import(Request $request, Supplier $supplier)
    {
        $request->validate([
            'strategy' => 'nullable|in:overwrite,skip,reject,duplicate',
            'layups' => 'required|array',
            'layups.*.name' => 'required|string',
            'layups.*.layers' => 'required|array',
            'layups.*.layers.*.layer_order' => 'required|integer',
            'layups.*.layers.*.thickness' => 'required|numeric',
            'layups.*.layers.*.width' => 'required|numeric',
            'layups.*.layers.*.angle' => 'required|numeric',
        ]);

        $strategy = $request->input('strategy', 'overwrite');
        $data = $request->input('layups');

        DB::beginTransaction();

        try {
            $conflicts = [];

            foreach ($data as $layupData) {

                $layup = $supplier->layups()
                    ->where('name', $layupData['name'])
                    ->first();

                if ($layup && $strategy === 'duplicate') {
                    $layup = $supplier->layups()->create([
                        'name' => $layupData['name'] . ' (imported)'
                    ]);
                }

                if (!$layup) {
                    $layup = $supplier->layups()->create([
                        'name' => $layupData['name']
                    ]);
                }

                foreach ($layupData['layers'] as $layerData) {

                    $existingLayer = $layup->layers()
                        ->where('layer_order', $layerData['layer_order'])
                        ->first();

                    if ($existingLayer) {

                        $isDifferent =
                                    (float)$existingLayer->thickness !== (float)$layerData['thickness'] ||
                                    (float)$existingLayer->width !== (float)$layerData['width'] ||
                                    (float)$existingLayer->angle !== (float)$layerData['angle'];

                        if ($isDifferent) {

                            $conflicts[] = [
                                'layup' => $layup->name,
                                'layer_order' => $layerData['layer_order']
                            ];

                            if ($strategy === 'overwrite') {
                                $existingLayer->update($layerData);
                            }

                            if ($strategy === 'skip') {
                                continue;
                            }

                            if ($strategy === 'reject') {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Conflict detected',
                                    'conflicts' => $conflicts
                                ], 409);
                            }
                        }

                    } else {
                        $layup->layers()->create($layerData);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Import success',
                'strategy' => $strategy,
                'total_layups' => count($data),
                'conflicts_detected' => count($conflicts)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // EXPORT
    public function export(Supplier $supplier)
    {
        $supplier->load('layups.layers');

        return response()->json([
            'supplier' => $supplier->name,
            'layups' => $supplier->layups
        ]);
    }
}