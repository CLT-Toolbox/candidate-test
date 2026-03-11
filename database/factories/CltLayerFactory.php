<?php

namespace Database\Factories;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayer>
 */
class CltLayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layup_id' => CltLayup::factory(),
            'thickness' => fake()->randomFloat(2),
            'width' => fake()->randomFloat(2),
            'angle' => fake()->randomFloat(2, 0, 360),
        ];
    }
}
