<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

class SupplierExportService
{
    public function exportAsJson(Supplier $supplier): array
    {
        return [
            'supplier' => [
                'name' => $supplier->name,
                'exported_at' => now()->toIso8601String(),
            ],
            'layups' => $supplier->layups->map(function ($layup) {
                return [
                    'name' => $layup->name,
                    'layers' => $layup->layers->map(function ($layer) {
                        return [
                            'layer_order' => $layer->layer_order,
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                        ];
                    })->sortBy('layer_order')->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    public function download(Supplier $supplier)
    {
        $data = $this->exportAsJson($supplier);
        $filename = "supplier-{$supplier->id}-{$supplier->name}-" . now()->format('Y-m-d-His') . '.json';

        return [
            'filename' => $filename,
            'content' => json_encode($data),
        ];
    }
}
