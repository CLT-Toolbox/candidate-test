<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupplierExport;

class SupplierDataController extends Controller
{
    public function exportJson($id)
    {
        try {
            $supplier = $this->getSupplierData($id);

            return response()->json($supplier, 200, [
                'Content-Disposition' => 'attachment; filename="supplier.json"'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export JSON: ' . $e->getMessage());
        }
    }

    public function exportCsv($id)
    {
        try {
            $supplier = $this->getSupplierData($id);

            $filename = "supplier.csv";
            $rows = [];

            $rows[] = ['Layup', 'Layer Order', 'Thickness', 'Width', 'Angle'];

            $layups = data_get($supplier, 'supplier.layups', []);

            foreach ($layups as $layup) {
                foreach (data_get($layup, 'layers', []) as $layer) {
                    $rows[] = [
                        $layup['name'] ?? '',
                        $layer['layer_order'] ?? '',
                        $layer['thickness'] ?? '',
                        $layer['width'] ?? '',
                        $layer['angle'] ?? '',
                    ];
                }
            }

            $handle = fopen('php://temp', 'r+');

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            rewind($handle);

            return response()->stream(function () use ($handle) {
                fpassthru($handle);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export CSV: ' . $e->getMessage());
        }
    }

    public function exportExcel($id)
    {
        try {
            $supplier = Supplier::with('layups.layers')->findOrFail($id);

            return Excel::download(
                new SupplierExport($supplier),
                'supplier.xlsx'
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export Excel: ' . $e->getMessage());
        }
    }

    public function importForm()
    {
        return view('suppliers.import');
    }

    public function import(Request $request, $supplierId)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
            'strategy' => 'required|in:overwrite,skip,duplicate,reject,manual'
        ]);

        $supplier = Supplier::with('layups.layers')->findOrFail($supplierId);

        $json = json_decode(file_get_contents($request->file('file')), true);

        if (!isset($json['supplier']['layups'])) {
            return back()->with('error', 'Invalid JSON format');
        }

        $conflicts = [];
        $processed = [];

        foreach ($json['supplier']['layups'] as $layupData) {

            $layup = $supplier->layups()
                ->whereRaw('LOWER(name) = ?', [strtolower($layupData['name'])])
                ->first();

            if ($layup && $request->strategy === 'duplicate') {
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

                $layerOrder = (int) $layerData['layer_order'];

                $existingLayer = $layup->layers()
                    ->where('layer_order', $layerOrder)
                    ->first();

                if (!$existingLayer) {
                    $layup->layers()->create($this->mapLayer($layerData));
                    $processed[] = $layerData;
                    continue;
                }

                $isConflict = $this->isLayerConflict($existingLayer, $layerData);

                if (!$isConflict) {
                    continue;
                }

                $conflict = $this->formatConflict($existingLayer, $layerData);

                switch ($request->strategy) {

                    case 'overwrite':
                        $existingLayer->update($this->mapLayer($layerData));
                        break;

                    case 'skip':
                        $conflicts[] = $conflict;
                        break;

                    case 'reject':
                        return response()->json([
                            'status' => 'rejected',
                            'conflicts' => [$conflict]
                        ], 422);

                    case 'manual':
                        $conflicts[] = $conflict;
                        break;
                }
            }
        }

        return redirect()
            ->back()
            ->with('conflicts', $conflicts);
    }

    private function isLayerConflict($existing, $incoming)
    {
        return (
            (float)$existing->thickness !== (float)$incoming['thickness'] ||
            (float)$existing->width !== (float)$incoming['width'] ||
            (float)$existing->angle !== (float)$incoming['angle']
        );
    }

    private function mapLayer($row)
    {
        return [
            'layer_order' => (int)$row['layer_order'],
            'thickness' => (float)$row['thickness'],
            'width' => (float)$row['width'],
            'angle' => (float)$row['angle'],
        ];
    }

    private function formatConflict($existing, $incoming)
    {
        return [
            'layer_id' => $existing->id,
            'layer_order' => $existing->layer_order,
            'existing' => [
                'thickness' => $existing->thickness,
                'width' => $existing->width,
                'angle' => $existing->angle,
            ],
            'incoming' => [
                'thickness' => $incoming['thickness'],
                'width' => $incoming['width'],
                'angle' => $incoming['angle'],
            ],
            'diff' => [
                'thickness' => $existing->thickness != $incoming['thickness'],
                'width' => $existing->width != $incoming['width'],
                'angle' => $existing->angle != $incoming['angle'],
            ]
        ];
    }

    public function resolveConflict(Request $request)
    {
        $layer = \App\Models\CltLayer::findOrFail($request->layer_id);

        if ($request->action === 'accept') {
            $layer->update([
                'thickness' => $request->thickness,
                'width' => $request->width,
                'angle' => $request->angle,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function getSupplierData($id)
    {
        $supplier = Supplier::with('layups.layers')->findOrFail($id);

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'layups' => $supplier->layups->map(function ($layup) {
                    return [
                        'id' => $layup->id,
                        'name' => $layup->name,
                        'layers' => $layup->layers->map(function ($layer) {
                            return [
                                'layer_order' => $layer->layer_order,
                                'thickness' => $layer->thickness,
                                'width' => $layer->width,
                                'angle' => $layer->angle,
                            ];
                        })
                    ];
                })
            ]
        ];
    }
}
