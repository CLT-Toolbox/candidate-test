<?php

namespace App\Services;

use App\Repositories\Interfaces\SupplierRepositoryInterface;

class SupplierService extends BaseService
{
    public function __construct(SupplierRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function paginateWithFilters(array $filters = [], int $perPage = 10)
    {
        return $this->repository->getPaginatedWithCounts($filters, $perPage);
    }
}
