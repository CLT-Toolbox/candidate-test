<?php

namespace Database\Factories;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CltLayerFactory extends Factory
{
    protected $model = CltLayer::class;

    public function definition(): array
    {
        static $layerOrder = 0;
        $layerOrder++;

        return [
            'layup_id' => CltLayup::factory(),
            'layer_order' => $layerOrder,
            'thickness' => $this->faker->randomElement([30.0, 35.0, 40.0, 45.0, 50.0]),
            'width' => $this->faker->randomElement([600, 900, 1200, 1500]),
            'angle' => $this->faker->randomElement([0, 90]),
            'species_grade' => $this->faker->randomElement(['C24', 'C16', 'Spruce', null]),
        ];
    }
}