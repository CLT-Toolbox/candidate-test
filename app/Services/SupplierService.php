<?php

namespace App\Services;

use App\Contracts\Services\SupplierServiceInterface;
use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService implements SupplierServiceInterface
{
    public function index(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        return Supplier::query()
            ->with(['layups.layers'])
            ->when(
                $filters['name'] ?? null,
                fn ($query, $name) => $query->where('name', 'like', '%'.$name.'%')
            )
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function store(array $data): Supplier
    {
        return Supplier::query()->create($data)->load('layups.layers');
    }

    public function show(Supplier $supplier): Supplier
    {
        return $supplier->loadMissing('layups.layers');
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->load('layups.layers');
    }

    public function destroy(Supplier $supplier): void
    {
        $supplier->delete();
    }
}
