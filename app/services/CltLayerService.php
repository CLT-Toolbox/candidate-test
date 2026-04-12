<?php

namespace App\Services;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayerService
{
    public function __construct(
        private CltLayerRepositoryInterface $layerRepository
    ) {}

    public function findByLayup(CltLayup $layup): Collection
    {
        return $this->layerRepository->findByLayup($layup);
    }

    public function findById(int $id): ?CltLayer
    {
        return $this->layerRepository->findById($id);
    }

    public function create(array $data): CltLayer
    {
        return $this->layerRepository->create($data);
    }

    public function update(CltLayer $layer, array $data): CltLayer
    {
        return $this->layerRepository->update($layer, $data);
    }

    public function delete(CltLayer $layer): bool
    {
        return $this->layerRepository->delete($layer);
    }
}
