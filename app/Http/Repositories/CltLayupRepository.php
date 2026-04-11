<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Models\CltLayup;

class CltLayupRepository implements CltLayupRepositoryInterface
{
   public function all($supplier_id)
   {
      return CltLayup::where('supplier_id', $supplier_id)->orderBy('name', 'asc')->get();
   }

   public function findById($id)
   {
      return CltLayup::findOrFail($id);
   }

   public function createOrUpdate(array $data, $id = null)
   {
      return CltLayup::updateOrCreate(['id' => $id], $data);
   }

   public function delete($id)
   {
      return CltLayup::findOrFail($id)->delete();
   }
}
