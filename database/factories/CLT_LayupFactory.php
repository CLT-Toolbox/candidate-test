<?php

namespace Database\Factories;

use App\Models\CLT_Layup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CLT_LayupFactory extends Factory
{
    protected $model = CLT_Layup::class;

    public function definition()
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => $this->faker->word() . ' Layup',
        ];
    }
}
