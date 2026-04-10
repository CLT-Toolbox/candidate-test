<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Contracts\SupplierRepositoryInterface;
use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInterface
{
   public function all()
   {
      return Supplier::with('layups')->orderBy('name', 'asc')->get();
   }

   public function findById($id)
   {
      return Supplier::findOrFail($id);
   }

   public function createOrUpdate(array $data, $id = null)
   {
      return Supplier::updateOrCreate(['id' => $id], $data);
   }

   public function delete($id)
   {
      return Supplier::findOrFail($id)->delete();
   }

   public function getWithLayupsAndLayers($supplierId)
   {
      return Supplier::with(['layups.layers'])->findOrFail($supplierId);
   }
}
