<?php

namespace App\Services;

use App\Http\Repositories\Contracts\CltLayupRepositoryInterface;

class CltLayupService
{
   public function __construct(protected CltLayupRepositoryInterface $repo) {}

   public function getAll($supplier_id)
   {
      return $this->repo->all($supplier_id);
   }

   public function getById($id)
   {
      return $this->repo->findById($id);
   }

   public function save(array $data, $id = null)
   {
      return $this->repo->createOrUpdate($data, $id);
   }

   public function remove($id)
   {
      return $this->repo->delete($id);
   }
}
