<?php

namespace App\Services;

use App\Models\Supplier;

class ExportService
{
    public function exportToResponse(Supplier $supplier): array
    {
        $supplier->load('layups.layers');
        
        return [
            'supplier_name' => $supplier->name,
            'exported_at'   => now()->toDateTimeString(),
            'layups'        => $supplier->layups->map(function ($layup) {
                return [
                    'name'   => $layup->name,
                    'layers' => $layup->layers->map(function ($layer) {
                        return [
                            'layer_order' => $layer->layer_order,
                            'thickness'   => $layer->thickness,
                            'width'       => $layer->width,
                            'angle'       => $layer->angle,
                        ];
                    }),
                ];
            }),
        ];
    }
}
