<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'PT CLT Toolbox Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Mitra Teknik Industri',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV Sumber Jaya Tools',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Global Industrial Supply',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Nusantara Engineering Solution',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
