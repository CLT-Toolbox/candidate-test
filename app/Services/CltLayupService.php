<?php

namespace App\Services;

use App\Exports\CltLayupExport;
use App\Interfaces\CltLayupRepositoryInterface;
use App\Models\Supplier;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class CltLayupService {
    public function __construct(
        protected CltLayupRepositoryInterface $cltLayupRepository
    ) {}

    public function exportToXlxs(Supplier $supplier)
    {
        $file_name = 'Layups_'. $supplier->name . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new CltLayupExport($this->cltLayupRepository, $supplier), $file_name);
    }
}
