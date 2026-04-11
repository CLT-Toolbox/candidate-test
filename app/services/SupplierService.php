<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->supplierRepository->paginate($perPage);
    }

    public function findById(int $id): ?Supplier
    {
        return $this->supplierRepository->findById($id);
    }

    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->supplierRepository->delete($supplier);
    }
}
