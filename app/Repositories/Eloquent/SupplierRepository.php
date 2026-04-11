<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function paginate(int $perPage = 10, string $sort = 'created'): LengthAwarePaginator
    {
        $query = Supplier::withCount('layups');

        match($sort) {
            'recent' => $query->latest('updated_at'),
            'created' => $query->latest('created_at'),
            'updated' => $query->latest('updated_at'),
            'name' => $query->orderBy('name'),
            default => $query->latest('created_at'),
        };

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Supplier
    {
        return Supplier::find($id);
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier;
    }

    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}
