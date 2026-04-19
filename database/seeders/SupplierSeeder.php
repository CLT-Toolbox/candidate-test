<?php

namespace Database\Seeders;

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
            ->count(30)
            ->create()
            ->each(function ($supplier) {
                \App\Models\CltLayup::factory()
                    ->count(rand(3, 5))
                    ->create(['supplier_id' => $supplier->id])
                    ->each(function ($layup) {
                        $layerCount = fake()->randomElement([3, 5, 7, 9]);
                        for ($i = 1; $i <= $layerCount; $i++) {
                            \App\Models\CltLayer::factory()->create([
                                'layup_id' => $layup->id,
                                'layer_order' => $i
                            ]);
                        }
                    });
            });
    }
}
