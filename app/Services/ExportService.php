<?php

namespace App\Services;

use App\Contracts\Services\ExportServiceInterface;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ExportService implements ExportServiceInterface
{
    public function export(Supplier $supplier): Supplier
    {
        return DB::transaction(
            fn (): Supplier => $supplier->fresh()->load(['layups.layers'])
        );
    }
}
