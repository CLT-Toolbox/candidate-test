<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Support\Facades\DB;

class SupplierExchangeService
{
        public function exportSupplier($supplierId)
    {
        return Supplier::with(['layups.layers'])->find($supplierId);
    }

    public function importData($data)
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::firstOrCreate(['name' => $data['name']]);

            foreach ($data['layups'] as $layupData) {
                $layup = CltLayup::updateOrCreate(
                    ['supplier_id' => $supplier->id, 'name' => $layupData['name']],
                    ['name' => $layupData['name']]
                );

                foreach ($layupData['layers'] as $layerData) {
                    CltLayer::updateOrCreate(
                        ['layup_id' => $layup->id, 'layer_order' => $layerData['layer_order']],
                        [
                            'thickness' => $layerData['thickness'],
                            'width' => $layerData['width'],
                            'angle' => $layerData['angle'],
                        ]
                    );
                }
            }
            return $supplier;
        });
    }
}