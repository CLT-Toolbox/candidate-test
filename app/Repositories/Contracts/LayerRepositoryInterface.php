<?php

namespace App\Repositories\Contracts;

use App\Models\Layer;
use App\Models\Layup;

interface LayerRepositoryInterface
{
    public function createForLayup(Layup $layup, array $data): Layer;

    public function update(Layer $layer, array $data): Layer;

    public function delete(Layer $layer): void;

    public function findByLayupAndOrder(Layup $layup, int $layerOrder): ?Layer;
}
