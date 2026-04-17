<?php

namespace Database\Factories;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayupFactory extends Factory
{
    protected $model = Layup::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => $this->faker->unique()->word(),
        ];
    }
}
