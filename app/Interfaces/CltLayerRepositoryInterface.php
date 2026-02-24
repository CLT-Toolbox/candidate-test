<?php

namespace App\Interfaces;

use App\Models\CltLayer;
use App\Models\CltLayup;

interface CltLayerRepositoryInterface {
    public function getPaginate($perPage = 10, CltLayup $layup);
    public function findById($id);
    public function create(CltLayup $layup, array $details);
    public function update(CltLayer $layer, array $newDetails);
    public function delete(CltLayer $layer);
}
