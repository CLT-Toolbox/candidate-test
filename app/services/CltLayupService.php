<?php

namespace App\Services;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayupService
{
    public function __construct(
        private CltLayupRepositoryInterface $layupRepository
    ) {}

    public function findBySupplier(Supplier $supplier): Collection
    {
        return $this->layupRepository->findBySupplier($supplier);
    }

    public function findById(int $id): ?CltLayup
    {
        return $this->layupRepository->findById($id);
    }

    public function create(array $data): CltLayup
    {
        return $this->layupRepository->create($data);
    }

    public function update(CltLayup $layup, array $data): CltLayup
    {
        return $this->layupRepository->update($layup, $data);
    }

    public function delete(CltLayup $layup): bool
    {
        return $this->layupRepository->delete($layup);
    }
}
