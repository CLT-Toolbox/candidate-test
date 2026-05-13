<?php

namespace App\Repositories\Contracts;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

interface LayupRepositoryInterface
{
    /**
     * @return Collection<int, Layup>
     */
    public function getBySupplier(Supplier $supplier): Collection;

    public function findForSupplierOrFail(Supplier $supplier, int $layupId): Layup;

    public function findByNameInSupplier(Supplier $supplier, string $name): ?Layup;

    /**
     * @param array<string, mixed> $attributes
     */
    public function createForSupplier(Supplier $supplier, array $attributes): Layup;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Layup $layup, array $attributes): bool;

    public function delete(Layup $layup): bool;
}
