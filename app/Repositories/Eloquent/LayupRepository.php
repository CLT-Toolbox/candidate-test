<?php

namespace App\Repositories\Eloquent;

use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayupRepository implements LayupRepositoryInterface
{
    /**
     * Mengambil semua layup dalam supplier.
     * Termasuk jumlah layers, diurutkan by name.
     */
    public function getBySupplier(Supplier $supplier): Collection
    {
        return $supplier->layups()
            ->withCount('layers')
            ->orderBy('name')
            ->get();
    }

    /**
     * Mencari layup by ID dalam supplier tertentu dengan eager load layers.
     * Throw exception jika tidak ditemukan.
     */
    public function findForSupplierOrFail(Supplier $supplier, int $layupId): Layup
    {
        return $supplier->layups()
            ->with(['layers' => fn ($query) => $query->orderBy('layer_order')])
            ->findOrFail($layupId);
    }

    /**
     * Mencari layup berdasarkan nama dalam supplier tertentu.
     * Return null jika tidak ditemukan.
     * Digunakan untuk deteksi konflik saat import.
     */
    public function findByNameInSupplier(Supplier $supplier, string $name): ?Layup
    {
        return $supplier->layups()->where('name', $name)->first();
    }

    /**
     * Membuat layup baru dalam supplier.
     */
    public function createForSupplier(Supplier $supplier, array $attributes): Layup
    {
        return $supplier->layups()->create($attributes);
    }

    /**
     * Update data layup.
     */
    public function update(Layup $layup, array $attributes): bool
    {
        return $layup->update($attributes);
    }

    /**
     * Menghapus layup.
     */
    public function delete(Layup $layup): bool
    {
        return $layup->delete();
    }
}
