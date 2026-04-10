<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Contracts\CltLayerRepositoryInterface;
use App\Models\CltLayer;

class CltLayerRepository implements CltLayerRepositoryInterface
{
   public function all($layup_id)
   {
      return CltLayer::where('layup_id', $layup_id)->orderBy('layer_order', 'asc')->get();
   }

   public function findById($id)
   {
      return CltLayer::findOrFail($id);
   }

   public function createOrUpdate(array $data, $id = null)
   {
      return CltLayer::updateOrCreate(['id' => $id], $data);
   }

   public function delete($id)
   {
      return CltLayer::findOrFail($id)->delete();
   }
}
