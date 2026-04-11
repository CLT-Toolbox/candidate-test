<?php

namespace App\Http\Repositories\Contracts;

interface CltLayerRepositoryInterface
{
   public function all($layup_id);
   public function findById($id);
   public function createOrUpdate(array $data, $id = null);
   public function delete($id);
}
