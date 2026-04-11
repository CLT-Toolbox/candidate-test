<?php

namespace App\Repositories\Contracts;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

interface CltLayupRepositoryInterface
{
    public function findBySupplier(Supplier $supplier): Collection;
    public function findById(int $id): ?CltLayup;
    public function findByNameAndSupplier(string $name, int $supplierId): ?CltLayup;
    public function create(array $data): CltLayup;
    public function update(CltLayup $layup, array $data): CltLayup;
    public function delete(CltLayup $layup): bool;
}
