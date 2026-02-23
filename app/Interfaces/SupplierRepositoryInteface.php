<?php

namespace App\Interfaces;

use App\Models\Supplier;

interface SupplierRepositoryInteface {
    public function getPaginate($perPage = 10, $search = null);
    public function findById($id);
    public function create(array $details);
    public function update(Supplier $supplier, array $newDetails);
    public function delete(Supplier $supplier);
}
