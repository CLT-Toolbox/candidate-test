<?php

namespace Database\Factories;

use App\Models\CltLayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayer>
 */
class CltLayerFactory extends Factory
{
    protected $model = CltLayer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layer_order' => 1,
            'thickness' => $this->faker->randomElement([20, 30, 40, 60]),
            'width' => $this->faker->randomElement([80, 100, 120, 140]),
            'angle' => $this->faker->randomElement([0, 90]),
        ];
    }
}
