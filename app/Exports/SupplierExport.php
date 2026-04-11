<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierExport implements WithMultipleSheets
{
    public function __construct(protected Supplier $supplier) {}

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new SupplierInfoSheet($this->supplier);

        foreach ($this->supplier->layups as $layup) {
            $sheets[] = new LayupSheet($layup);
        }

        return $sheets;
    }
}
