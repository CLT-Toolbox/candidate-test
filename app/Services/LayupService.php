<?php

namespace App\Services;

use App\Models\Layup;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayupService
{
    public function __construct(
        private LayupRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function findBySupplier(int $supplierId): Collection
    {
        return $this->repository->findBySupplier($supplierId);
    }

    public function findOrFail(int $id): Layup
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Layup
    {
        return $this->repository->create($data);
    }

    public function update(Layup $layup, array $data): Layup
    {
        return $this->repository->update($layup, $data);
    }

    public function delete(Layup $layup): bool
    {
        return $this->repository->delete($layup);
    }
}
