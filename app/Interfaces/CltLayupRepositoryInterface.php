<?php

namespace App\Interfaces;

use App\Models\CltLayup;
use App\Models\Supplier;

interface CltLayupRepositoryInterface {
    public function getPaginate($perPage = 10, $search = null, Supplier $supplier);
    public function findById($id);
    public function create(Supplier $supplier, array $details);
    public function update(CltLayup $layup, array $newDetails);
    public function delete(CltLayup $layup);
    public function getExport(Supplier $supplier);
}
