<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::create([
            'id' => 1,
            'name' => 'PT. CLT Indonesia'
        ]);

        $layup = CltLayup::create([
            'id' => 1,
            'supplier_id' => $supplier->id,
            'name' => 'Layup Utama'
        ]);

        CltLayer::create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 15.5,
            'width' => 100,
            'angle' => 90
        ]);

        CltLayer::create([
            'layup_id' => $layup->id,
            'layer_order' => 2,
            'thickness' => 20.0,
            'width' => 120,
            'angle' => 180
        ]);

        $this->command->info('Data Dummy Supplier, Layup, dan Layer berhasil dibuat!');
    }
}