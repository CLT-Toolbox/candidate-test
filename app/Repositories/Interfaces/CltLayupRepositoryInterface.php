<?php

namespace App\Repositories\Interfaces;

interface CltLayupRepositoryInterface extends EloquentRepositoryInterface 
{
    public function paginateBySupplier(int $supplierId, array $criteria = [], int $perPage = 10);
}
