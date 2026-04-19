<?php

namespace App\Repositories;

use App\Models\CltLayup;
use App\Repositories\Interfaces\CltLayupRepositoryInterface;

class CltLayupRepository extends BaseRepository implements CltLayupRepositoryInterface
{
    public function __construct(CltLayup $model)
    {
        parent::__construct($model);
    }

    public function paginateBySupplier(int $supplierId, array $criteria = [], int $perPage = 10)
    {
        $query = $this->model->where('supplier_id', $supplierId)
            ->withCount('layers')
            ->withSum('layers', 'thickness');

        if (!empty($criteria['search'])) {
            $query->where('name', 'like', '%' . $criteria['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }
}
