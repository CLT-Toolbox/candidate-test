<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SupplierExport implements FromArray
{
    protected $supplier;

    public function __construct($supplier)
    {
        $this->supplier = $supplier;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['Layup', 'Layer Order', 'Thickness', 'Width', 'Angle'];

        foreach ($this->supplier->layups as $layup) {
            foreach ($layup->layers as $layer) {
                $rows[] = [
                    $layup->name,
                    $layer->layer_order,
                    $layer->thickness,
                    $layer->width,
                    $layer->angle,
                ];
            }
        }

        return $rows;
    }
}
