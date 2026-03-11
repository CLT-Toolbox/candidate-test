<?php

namespace App\Services;

use App\Models\Layer;
use App\Repositories\Contracts\LayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayerService
{
    public function __construct(
        private LayerRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function findByLayup(int $layupId): Collection
    {
        return $this->repository->findByLayup($layupId);
    }

    public function findOrFail(int $id): Layer
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Layer
    {
        $existing = $this->repository->findByLayupAndOrder(
            $data['layup_id'],
            $data['layer_order']
        );

        if ($existing) {
            throw new \InvalidArgumentException('Layer order already exists in this layup.');
        }

        return $this->repository->create($data);
    }

    public function update(Layer $layer, array $data): Layer
    {
        $existing = Layer::where('layup_id', $layer->layup_id)
            ->where('layer_order', $data['layer_order'])
            ->where('id', '!=', $layer->id)
            ->first();

        if ($existing) {
            throw new \InvalidArgumentException('Layer order already used by another layer in this layup.');
        }

        return $this->repository->update($layer, $data);
    }

    public function delete(Layer $layer): bool
    {
        return $this->repository->delete($layer);
    }
}
