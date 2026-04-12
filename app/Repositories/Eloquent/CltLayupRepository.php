<?php

namespace App\Repositories\Eloquent;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayupRepository implements CltLayupRepositoryInterface
{
    public function findBySupplier(Supplier $supplier): Collection
    {
        return $supplier->layups()->with('layers')->get();
    }

    public function findById(int $id): ?CltLayup
    {
        return CltLayup::with('layers')->find($id);
    }

    public function findByNameAndSupplier(string $name, int $supplierId): ?CltLayup
    {
        return CltLayup::where('supplier_id', $supplierId)
            ->where('name', $name)
            ->first();
    }

    public function create(array $data): CltLayup
    {
        return CltLayup::create($data);
    }

    public function update(CltLayup $layup, array $data): CltLayup
    {
        $layup->update($data);
        return $layup->fresh();
    }

    public function delete(CltLayup $layup): bool
    {
        return $layup->delete();
    }
}
