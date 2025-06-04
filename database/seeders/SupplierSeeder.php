<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::truncate();

        Supplier::create([
            'name'          => 'Sodra',
            'material_type' => 'clt',
        ]);

        Supplier::create([
            'name'          => 'KLH',
            'material_type' => 'clt',
        ]);

        Supplier::create([
            'name'          => 'XLam',
            'material_type' => 'clt',
        ]);
        Supplier::create([
            'name'          => 'Kalvasta Timber',
            'material_type' => 'glt',
        ]);

        Supplier::create([
            'name'          => 'Timberlink',
            'material_type' => 'glt',
        ]);
    }
}
