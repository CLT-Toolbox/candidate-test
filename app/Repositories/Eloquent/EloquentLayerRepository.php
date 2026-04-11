<?php

namespace App\Repositories\Eloquent;

use App\Models\Layer;
use App\Models\Layup;
use App\Repositories\Contracts\LayerRepositoryInterface;

class EloquentLayerRepository implements LayerRepositoryInterface
{
    public function createForLayup(Layup $layup, array $data): Layer
    {
        return $layup->layers()->create($data);
    }

    public function update(Layer $layer, array $data): Layer
    {
        $layer->update($data);

        return $layer->refresh();
    }

    public function delete(Layer $layer): void
    {
        $layer->delete();
    }

    public function findByLayupAndOrder(Layup $layup, int $layerOrder): ?Layer
    {
        return $layup->layers()->where('layer_order', $layerOrder)->first();
    }
}
