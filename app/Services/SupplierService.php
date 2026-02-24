<?php

namespace App\Services;

use App\Exports\SupplierExport;
use App\Interfaces\SupplierRepositoryInteface;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SupplierService {
    public function __construct(
        protected SupplierRepositoryInteface $supplierRepository
    ) {}

    public function exportToCsv()
    {
        $fileName = 'supplier_export_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new SupplierExport($this->supplierRepository), $fileName);
    }
}
