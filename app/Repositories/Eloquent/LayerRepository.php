<?php

namespace App\Repositories\Eloquent;

use App\Models\Layer;
use App\Models\Layup;
use App\Repositories\Contracts\LayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LayerRepository implements LayerRepositoryInterface
{
    /**
     * Mengambil semua layer dalam layup.
     * Diurutkan berdasarkan layer_order.
     */
    public function getByLayup(Layup $layup): Collection
    {
        return $layup->layers()->orderBy('layer_order')->get();
    }

    /**
     * Mencari layer by ID dalam layup tertentu.
     * Throw exception jika tidak ditemukan.
     */
    public function findForLayupOrFail(Layup $layup, int $layerId): Layer
    {
        return $layup->layers()->findOrFail($layerId);
    }

    /**
     * Mencari layer berdasarkan layer_order dalam layup tertentu.
     * Return null jika tidak ditemukan.
     * Digunakan untuk deteksi konflik saat import.
     */
    public function findByOrderInLayup(Layup $layup, int $layerOrder): ?Layer
    {
        return $layup->layers()->where('layer_order', $layerOrder)->first();
    }

    /**
     * Membuat layer baru dalam layup.
     */
    public function createForLayup(Layup $layup, array $attributes): Layer
    {
        return $layup->layers()->create($attributes);
    }

    /**
     * Update data layer.
     */
    public function update(Layer $layer, array $attributes): bool
    {
        return $layer->update($attributes);
    }

    /**
     * Menghapus layer.
     */
    public function delete(Layer $layer): bool
    {
        return $layer->delete();
    }
}
