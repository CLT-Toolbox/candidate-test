<?php

namespace App\Http\Repositories\Contracts;

interface SupplierRepositoryInterface
{
   public function all();
   public function findById($id);
   public function createOrUpdate(array $data, $id = null);
   public function delete($id);
   public function getWithLayupsAndLayers($supplierId);
}
