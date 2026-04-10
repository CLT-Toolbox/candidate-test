<?php

namespace App\Http\Repositories\Contracts;

interface CltLayupRepositoryInterface
{
   public function all($supplier_id);
   public function findById($id);
   public function createOrUpdate(array $data, $id = null);
   public function delete($id);
}
