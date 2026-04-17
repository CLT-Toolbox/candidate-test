<?php

namespace App\Contracts\Services;

use App\Models\Supplier;

interface ExportServiceInterface
{
    public function export(Supplier $supplier): Supplier;
}
