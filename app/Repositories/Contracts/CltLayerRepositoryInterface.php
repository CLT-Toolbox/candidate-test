<?php

namespace App\Repositories\Contracts;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Collection;

interface CltLayerRepositoryInterface
{
    public function findByLayup(CltLayup $layup): Collection;
    public function findById(int $id): ?CltLayer;
    public function findByOrderAndLayup(int $order, int $layupId): ?CltLayer;
    public function create(array $data): CltLayer;
    public function update(CltLayer $layer, array $data): CltLayer;
    public function delete(CltLayer $layer): bool;
}
