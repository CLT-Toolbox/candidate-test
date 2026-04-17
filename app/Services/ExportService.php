<?php

namespace App\Services;

use App\Models\Supplier;

class ExportService
{
    /**
     * Export supplier with all layups and layers
     */
    public function export(Supplier $supplier): array
    {
        $supplier->load('layups.layers');

        return [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'layups' => $supplier->layups->map(function ($layup) {
                return [
                    'id' => $layup->id,
                    'name' => $layup->name,
                    'layers' => $layup->layers->map(function ($layer) {
                        return [
                            'id' => $layer->id,
                            'layer_order' => $layer->layer_order,
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }
}
