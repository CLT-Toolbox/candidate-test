<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SupplierService
{
    public function __construct(
        private SupplierRepositoryInterface $repository
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function findOrFail(int $id): Supplier
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Supplier
    {
        return $this->repository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->repository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repository->delete($supplier);
    }

    public function export(Supplier $supplier): array
    {
        $supplier->load('layups.layers');

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'layups' => $supplier->layups->map(fn($layup) => [
                'id' => $layup->id,
                'name' => $layup->name,
                'layers' => $layup->layers->map(fn($layer) => [
                    'id' => $layer->id,
                    'layer_order' => $layer->layer_order,
                    'thickness' => $layer->thickness,
                    'width' => $layer->width,
                    'angle' => $layer->angle,
                ])->toArray(),
            ])->toArray(),
        ];
    }
}
