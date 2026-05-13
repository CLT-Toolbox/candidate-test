<?php

namespace App\Repositories\Contracts;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Database\Eloquent\Collection;

interface LayerRepositoryInterface
{
    /**
     * @return Collection<int, Layer>
     */
    public function getByLayup(Layup $layup): Collection;

    public function findForLayupOrFail(Layup $layup, int $layerId): Layer;

    public function findByOrderInLayup(Layup $layup, int $layerOrder): ?Layer;

    /**
     * @param array<string, mixed> $attributes
     */
    public function createForLayup(Layup $layup, array $attributes): Layer;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Layer $layer, array $attributes): bool;

    public function delete(Layer $layer): bool;
}
