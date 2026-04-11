<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class LayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'layer_order' => 1, // override nanti
            'thickness' => fake()->randomElement([20, 40]),
            'width' => 1200,
            'angle' => fake()->randomElement([0, 90]),
        ];
    }
}
