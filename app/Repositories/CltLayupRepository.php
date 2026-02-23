<?php

namespace App\Repositories;

use App\Interfaces\CltLayupRepositoryInterface;
use App\Models\CltLayup;
use App\Models\Supplier;

class CltLayupRepository implements CltLayupRepositoryInterface
{
    public function getPaginate($perPage = 10, $search = null, Supplier $supplier)
    {
        return $supplier->cltLayups()
            ->withCount('cltLayers')
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return CltLayup::find($id);
    }

    public function create(Supplier $supplier, array $details)
    {
        return $supplier->cltLayups()->create($details);
    }

    public function update(CltLayup $layup, array $newDetails)
    {
        return $layup->update($newDetails);
    }

    public function delete(CltLayup $layup)
    {
        return $layup->delete();
    }
}
