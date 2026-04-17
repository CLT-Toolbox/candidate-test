<?php

namespace App\Contracts\Services;

use App\Models\Supplier;

interface ImportServiceInterface
{
    public function import(Supplier $supplier, array $data): array;
}
