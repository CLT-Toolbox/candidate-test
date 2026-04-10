<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CltLayupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = DB::table('suppliers')->get();

        foreach ($suppliers as $supplier) {
            DB::table('clt_layups')->insert([
                [
                    'supplier_id' => $supplier->id,
                    'name' => 'Standard 3 Layer - ' . $supplier->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'supplier_id' => $supplier->id,
                    'name' => 'Standard 5 Layer - ' . $supplier->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
