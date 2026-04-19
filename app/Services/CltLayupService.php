<?php

namespace App\Services;

use App\Repositories\Interfaces\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayupService extends BaseService
{
    public function __construct(CltLayupRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function getBySupplier(int $supplierId): Collection
    {
        return $this->repository->all(['*'], [], ['supplier_id' => $supplierId]);
    }

    public function paginateBySupplier(int $supplierId, array $filters = [], int $perPage = 10)
    {
        return $this->repository->paginateBySupplier($supplierId, $filters, $perPage);
    }

    public function duplicate(int $id)
    {
        $original = $this->repository->find($id, ['*'], ['layers']);

        $clone = $original->replicate();
        $clone->name = $original->name.' (Copy)';
        $clone->save();

        foreach ($original->layers as $layer) {
            $layerClone = $layer->replicate();
            $layerClone->layup_id = $clone->id;
            $layerClone->save();
        }

        return $clone;
    }
}
