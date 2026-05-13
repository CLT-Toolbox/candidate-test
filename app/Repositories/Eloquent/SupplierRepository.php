<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    /**
     * Mengambil supplier dengan pagination, termasuk jumlah layup.
     * Support search di field name, code, dan address.
     */
    public function paginateWithLayupCount(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Supplier::query()
            ->withCount('layups')
            ->when($search !== null && $search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhere('address', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Mencari supplier by ID.
     * Throw exception jika tidak ditemukan.
     */
    public function findOrFail(int $id): Supplier
    {
        return Supplier::query()->findOrFail($id);
    }

    /**
     * Mencari supplier by ID dengan eager load semua layups dan layers.
     * Layers diurutkan berdasarkan layer_order.
     * Throw exception jika tidak ditemukan.
     */
    public function findWithRelationsOrFail(int $id): Supplier
    {
        return Supplier::query()
            ->with(['layups.layers' => fn ($query) => $query->orderBy('layer_order')])
            ->findOrFail($id);
    }

    /**
     * Membuat supplier baru.
     */
    public function create(array $attributes): Supplier
    {
        return Supplier::query()->create($attributes);
    }

    /**
     * Update data supplier.
     */
    public function update(Supplier $supplier, array $attributes): bool
    {
        return $supplier->update($attributes);
    }

    /**
     * Menghapus supplier.
     */
    public function delete(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}
