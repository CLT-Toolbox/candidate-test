<?php

namespace App\Repositories;

use App\Interfaces\SupplierRepositoryInteface;
use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInteface
{
    public function getPaginate($perPage = 10, $search = null)
    {
        return Supplier::withCount('cltLayups')
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return Supplier::where('id', $id)->first();
    }

    public function create(array $details)
    {
        return Supplier::create($details);
    }

    public function update(Supplier $supplier, array $newDetails)
    {
        return $supplier->update($newDetails);
    }

    public function delete(Supplier $supplier)
    {
        return $supplier->delete();
    }
}
