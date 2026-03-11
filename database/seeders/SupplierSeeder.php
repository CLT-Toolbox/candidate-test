<?php

namespace Database\Seeders;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory()
            ->has(
                CltLayup::factory()->has(
                    CltLayer::factory()->count(3)
                )->count(3)
            )
            ->count(15)
            ->create();
    }
}
