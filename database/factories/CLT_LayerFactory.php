<?php

namespace Database\Factories;

use App\Models\CLT_Layer;
use App\Models\CLT_Layup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CLT_LayerFactory extends Factory
{
    protected $model = CLT_Layer::class;

    public function definition()
    {
        return [
            'layup_id' => CLT_Layup::factory(),
            'layer_order' => $this->faker->numberBetween(1, 10),
            'thickness' => $this->faker->randomFloat(2, 10, 50),
            'width' => $this->faker->randomFloat(2, 100, 200),
            'angle' => $this->faker->randomFloat(2, 0, 90),
        ];
    }
}
