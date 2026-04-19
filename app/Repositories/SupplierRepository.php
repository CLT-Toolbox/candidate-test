<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function getPaginatedWithCounts(array $criteria = [], int $perPage = 10)
    {
        $query = $this->model->withCount('layups');

        if (!empty($criteria['search'])) {
            $query->where('name', 'like', '%' . $criteria['search'] . '%');
        }

        return $query->latest()->paginate($perPage);
    }
}