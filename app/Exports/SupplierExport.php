<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromArray;

class SupplierExport implements FromArray
{
    public function array(): array
    {
        $suppliers = Supplier::with('layups.layers')->get();

        $data = [];

        // HEADER
        $data[] = [
            'Supplier',
            'Layup',
            'Layer Order',
            'Thickness',
            'Width',
            'Angle'
        ];

        foreach ($suppliers as $supplier) {
            foreach ($supplier->layups as $layup) {
                foreach ($layup->layers as $layer) {
                    $data[] = [
                        $supplier->name,
                        $layup->name,
                        $layer->layer_order,
                        $layer->thickness,
                        $layer->width,
                        $layer->angle,
                    ];
                }
            }
        }

        return $data;
    }
}
