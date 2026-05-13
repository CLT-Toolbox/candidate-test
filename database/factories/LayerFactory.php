<?php

namespace Database\Factories;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Layer>
 */
class LayerFactory extends Factory
{
    protected $model = Layer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layup_id' => Layup::factory(),
            'layer_order' => fake()->numberBetween(1, 50),
            'thickness' => fake()->randomFloat(2, 1, 30),
            'width' => fake()->randomFloat(2, 10, 200),
            'angle' => fake()->randomFloat(2, -90, 90),
        ];
    }
}
