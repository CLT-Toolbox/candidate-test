<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierExportService;
use Illuminate\Http\Response;

class SupplierExportController extends Controller
{
    protected SupplierExportService $exportService;

    public function __construct(SupplierExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export a supplier as JSON download
     */
    public function export(Supplier $supplier)
    {
        $export = $this->exportService->download($supplier);
        return response($export['content'], 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$export['filename']}\"",
        ]);
    }
}
