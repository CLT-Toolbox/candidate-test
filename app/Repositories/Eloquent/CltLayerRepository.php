<?php

namespace App\Repositories\Eloquent;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayerRepository implements CltLayerRepositoryInterface
{
    public function findByLayup(CltLayup $layup): Collection
    {
        return $layup->layers()->orderBy('layer_order')->get();
    }

    public function findById(int $id): ?CltLayer
    {
        return CltLayer::find($id);
    }

    public function findByOrderAndLayup(int $order, int $layupId): ?CltLayer
    {
        return CltLayer::where('layup_id', $layupId)
            ->where('layer_order', $order)
            ->first();
    }

    public function create(array $data): CltLayer
    {
        return CltLayer::create($data);
    }

    public function update(CltLayer $layer, array $data): CltLayer
    {
        $layer->update($data);
        return $layer->fresh();
    }

    public function delete(CltLayer $layer): bool
    {
        return $layer->delete();
    }
}
