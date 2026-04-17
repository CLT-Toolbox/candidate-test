<?php

namespace App\Contracts\Services;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierServiceInterface
{
    public function index(array $filters = []): LengthAwarePaginator;

    public function store(array $data): Supplier;

    public function show(Supplier $supplier): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function destroy(Supplier $supplier): void;
}
