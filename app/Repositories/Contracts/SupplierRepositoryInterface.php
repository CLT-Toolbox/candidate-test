<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function paginateWithCounts(int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function delete(Supplier $supplier): void;

    public function findWithRelations(Supplier $supplier): Supplier;
}
