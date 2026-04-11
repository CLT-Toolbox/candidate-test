<?php

namespace App\Repositories\Contracts;

use App\Models\Layup;
use App\Models\Supplier;

interface LayupRepositoryInterface
{
    public function createForSupplier(Supplier $supplier, array $data): Layup;

    public function update(Layup $layup, array $data): Layup;

    public function delete(Layup $layup): void;

    public function findBySupplierAndName(Supplier $supplier, string $name): ?Layup;
}
