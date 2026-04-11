<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $companies = [
            'Nordic Timber Co.',
            'Alpine CLT Solutions',
            'MassivWood Ltd.',
            'TimberStruct Inc.',
            'EuroLam Systems',
        ];

        return [
            'name' => fake()->unique()->randomElement($companies),
            'created_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }

    public function withLayups($count = 3)
    {
        return $this->has(
            \App\Models\Layup::factory()->count($count),
            'layups'
        );
    }
}
