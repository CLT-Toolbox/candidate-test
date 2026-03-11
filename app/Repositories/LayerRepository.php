<?php

namespace App\Repositories;

use App\Models\Layer;
use App\Repositories\Contracts\LayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayerRepository implements LayerRepositoryInterface
{
    public function all(): Collection
    {
        return Layer::with('layup')->get();
    }

    public function findByLayup(int $layupId): Collection
    {
        return Layer::where('layup_id', $layupId)->get();
    }

    public function findOrFail(int $id): Layer
    {
        return Layer::findOrFail($id);
    }

    public function create(array $data): Layer
    {
        return Layer::create($data);
    }

    public function update(Layer $layer, array $data): Layer
    {
        $layer->update($data);

        return $layer;
    }

    public function delete(Layer $layer): bool
    {
        return $layer->delete();
    }

    public function findByLayupAndOrder(int $layupId, int $layerOrder): ?Layer
    {
        return Layer::where('layup_id', $layupId)
            ->where('layer_order', $layerOrder)
            ->first();
    }
}
