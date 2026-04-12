<?php

namespace Database\Factories;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CltLayupFactory extends Factory
{
    protected $model = CltLayup::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => 'CLT-' . $this->faker->numberBetween(3, 7) . '-' . $this->faker->numberBetween(100, 300) . '-' . $this->faker->randomElement(['L', 'M', 'H']),
            'layup_code' => 'L-' . strtoupper($this->faker->bothify('###-?')),
            'revision' => $this->faker->randomElement(['Rev 1', 'Rev 2', null]),
            'status' => $this->faker->randomElement(['active', 'draft', 'archived']),
            'species_grade' => $this->faker->randomElement(['Spruce / No. 2', 'C24', 'C16', null]),
        ];
    }

    public function withLayers($count = 3): self
    {
        return $this->has(
            \App\Models\CltLayer::factory()->count($count),
            'layers'
        );
    }
}