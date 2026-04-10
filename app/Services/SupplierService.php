<?php

namespace App\Services;

use App\Exports\SupplierExport;
use App\Http\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SupplierService
{
   public function __construct(protected SupplierRepositoryInterface $repo) {}

   public function getAll()
   {
      return $this->repo->all();
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

   public function exportExcel($supplierId)
   {
      $supplier = $this->repo->getWithLayupsAndLayers($supplierId);
      $filename = 'supplier-' . Str::slug($supplier->name) . '-' . now()->format('Ymd') . '.xlsx';

      return Excel::download(new SupplierExport($supplier), $filename);
   }
}
