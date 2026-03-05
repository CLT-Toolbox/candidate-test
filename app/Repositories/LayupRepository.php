<?php

namespace App\Repositories;

use App\Models\Layup;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayupRepository implements LayupRepositoryInterface
{
    public function all(): Collection
    {
        return Layup::with('layers')->get();
    }

    public function findBySupplier(int $supplierId): Collection
    {
        return Layup::where('supplier_id', $supplierId)->with('layers')->get();
    }

    public function findOrFail(int $id): Layup
    {
        return Layup::findOrFail($id);
    }

    public function create(array $data): Layup
    {
        return Layup::create($data);
    }

    public function update(Layup $layup, array $data): Layup
    {
        $layup->update($data);

        return $layup;
    }

    public function delete(Layup $layup): bool
    {
        return $layup->delete();
    }
}
