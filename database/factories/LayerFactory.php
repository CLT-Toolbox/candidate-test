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

    public function definition(): array
    {
        return [
            'layup_id' => Layup::factory(),
            'layer_order' => 1,
            'thickness' => fake()->randomFloat(2, 20, 120),
            'width' => fake()->randomFloat(2, 80, 240),
            'angle' => fake()->randomElement([-45, 0, 45, 90]),
        ];
    }
}
