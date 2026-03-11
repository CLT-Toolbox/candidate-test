<?php

namespace App\Repositories\Contracts;

use App\Models\Layup;
use Illuminate\Database\Eloquent\Collection;

interface LayupRepositoryInterface
{
    public function all(): Collection;

    public function findBySupplier(int $supplierId): Collection;

    public function findOrFail(int $id): Layup;

    public function create(array $data): Layup;

    public function update(Layup $layup, array $data): Layup;

    public function delete(Layup $layup): bool;
}
