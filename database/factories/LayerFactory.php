<?php

namespace Database\Factories;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayerFactory extends Factory
{
    protected $model = Layer::class;

    public function definition(): array
    {
        return [
            'layup_id' => Layup::factory(),
            'layer_order' => $this->faker->unique()->numberBetween(1, 20),
            'thickness' => $this->faker->randomFloat(2, 0.1, 10),
            'width' => $this->faker->randomFloat(2, 1, 100),
            'angle' => $this->faker->randomFloat(2, -90, 90),
        ];
    }
}
