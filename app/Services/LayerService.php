<?php

namespace App\Services;

use App\Contracts\Services\LayerServiceInterface;
use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LayerService implements LayerServiceInterface
{
    public function index(Layup $layup, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $layup->layers()
            ->when(
                $filters['layer_order'] ?? null,
                fn ($query, $layerOrder) => $query->where('layer_order', $layerOrder)
            )
            ->orderBy('layer_order')
            ->paginate($perPage);
    }

    public function store(Layup $layup, array $data): Layer
    {
        return $layup->layers()->create($data)->load('layup');
    }

    public function show(Layer $layer): Layer
    {
        return $layer->loadMissing('layup.supplier');
    }

    public function update(Layer $layer, array $data): Layer
    {
        $layer->update($data);

        return $layer->load('layup.supplier');
    }

    public function destroy(Layer $layer): void
    {
        $layer->delete();
    }
}
