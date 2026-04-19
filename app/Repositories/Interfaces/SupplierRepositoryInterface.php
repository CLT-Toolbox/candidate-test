<?php

namespace App\Repositories\Interfaces;

interface SupplierRepositoryInterface extends EloquentRepositoryInterface 
{
    public function getPaginatedWithCounts(array $criteria = [], int $perPage = 10);
}
