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
        $suppliers = [
            ['name' => 'Nordic Timber Co.'],
            ['name' => 'Alpine CLT Solutions'],
            ['name' => 'MassivWood Ltd.'],
            ['name' => 'TimberStruct Inc.'],
            ['name' => 'EuroLam Systems'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
