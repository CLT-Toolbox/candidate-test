<?php

namespace Database\Factories;

use App\Models\Layup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Layer>
 */
class LayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 0;
        return [
            'layup_id' => Layup::factory(),
            'layer_order' => ++$sequence,
            'thickness' => $this->faker->randomFloat(4, 0.1, 10),
            'width' => $this->faker->randomFloat(4, 0.1, 100),
            'angle' => $this->faker->randomFloat(4, 0, 360),
        ];
    }
}
