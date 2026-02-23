<?php

namespace App\Services;

use App\Interfaces\SupplierRepositoryInteface;

class SupplierService {
    public function __construct(
        protected SupplierRepositoryInteface $supplierRepository
    ) {}
}
