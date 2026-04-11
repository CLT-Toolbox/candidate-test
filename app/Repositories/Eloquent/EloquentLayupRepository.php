<?php

namespace App\Repositories\Eloquent;

use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayupRepositoryInterface;

class EloquentLayupRepository implements LayupRepositoryInterface
{
    public function createForSupplier(Supplier $supplier, array $data): Layup
    {
        return $supplier->layups()->create($data);
    }

    public function update(Layup $layup, array $data): Layup
    {
        $layup->update($data);

        return $layup->refresh();
    }

    public function delete(Layup $layup): void
    {
        $layup->delete();
    }

    public function findBySupplierAndName(Supplier $supplier, string $name): ?Layup
    {
        return $supplier->layups()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->with('layers')
            ->first();
    }
}
