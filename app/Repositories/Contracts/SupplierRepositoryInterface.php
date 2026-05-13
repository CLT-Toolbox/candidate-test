<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function paginateWithLayupCount(?string $search = null, int $perPage = 10): LengthAwarePaginator;

    public function findOrFail(int $id): Supplier;

    public function findWithRelationsOrFail(int $id): Supplier;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Supplier;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Supplier $supplier, array $attributes): bool;

    public function delete(Supplier $supplier): bool;
}
