<?php

namespace App\Repositories\Contracts;

use App\Models\Layer;
use Illuminate\Database\Eloquent\Collection;

interface LayerRepositoryInterface
{
    public function all(): Collection;

    public function findByLayup(int $layupId): Collection;

    public function findOrFail(int $id): Layer;

    public function create(array $data): Layer;

    public function update(Layer $layer, array $data): Layer;

    public function delete(Layer $layer): bool;

    public function findByLayupAndOrder(int $layupId, int $layerOrder): ?Layer;
}
