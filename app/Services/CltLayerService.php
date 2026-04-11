<?php

namespace App\Services;

use App\Http\Repositories\Contracts\CltLayerRepositoryInterface;

class CltLayerService
{
   public function __construct(protected CltLayerRepositoryInterface $repo) {}

   public function getAll($layup_id)
   {
      return $this->repo->all($layup_id);
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
