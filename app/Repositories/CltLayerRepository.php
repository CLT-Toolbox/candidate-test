<?php

namespace App\Repositories;

use App\Interfaces\CltLayerRepositoryInterface;
use App\Models\CltLayer;
use App\Models\CltLayup;

class CltLayerRepository implements CltLayerRepositoryInterface
{
    public function getPaginate($perPage = 10, CltLayup $layup)
    {
        return $layup->cltLayers()
            ->orderBy('layer_order', 'asc')
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return CltLayer::find($id);
    }

    public function create(CltLayup $layup, array $details)
    {
        return $layup->cltLayers()->create($details);
    }

    public function update(CltLayer $layer, array $newDetails)
    {
        return $layer->update($newDetails);
    }

    public function delete(CltLayer $layer)
    {
        return $layer->delete();
    }
}
