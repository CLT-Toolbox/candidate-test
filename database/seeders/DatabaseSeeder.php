<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Layup;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ USER LOGIN
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // ✅ SUPPLIERS + LAYUPS + LAYERS
        Supplier::factory()
            ->count(5)
            ->create()
            ->each(function ($supplier) {

                $layups = Layup::factory()
                    ->count(3)
                    ->create([
                        'supplier_id' => $supplier->id
                    ]);

                foreach ($layups as $layup) {

                    $layerCount = rand(3, 5);

                    for ($i = 1; $i <= $layerCount; $i++) {
                        $layup->layers()->create([
                            'layer_order' => $i,
                            'thickness' => fake()->randomElement([20, 40]),
                            'width' => 1200,
                            'angle' => $i % 2 === 0 ? 90 : 0,
                        ]);
                    }
                }
            });
    }
}
