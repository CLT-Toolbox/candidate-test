<?php

namespace App\Services;

use App\Models\Supplier;

class ExportService
{
    public function exportSupplier(Supplier $supplier): array
    {
        $supplier->load('layups.layers');
        
        return [
            'supplier' => [
                'name' => $supplier->name
            ],
            'layups' => $supplier->layups->map(function($layup) {
                return [
                    'name' => $layup->name,
                    'layers' => $layup->layers->map(function($layer) {
                        return [
                            'layer_order' => $layer->layer_order,
                            'thickness' => $layer->thickness,
                            'width' => $layer->width,
                            'angle' => $layer->angle
                        ];
                    })->toArray()
                ];
            })->toArray()
        ];
    }

    public function download(Supplier $supplier)
    {
        $data = $this->exportSupplier($supplier);
        $filename = 'supplier-' . $supplier->id . '-' . time() . '.json';
        
        return response()->json($data, 200, [], JSON_PRETTY_PRINT)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}