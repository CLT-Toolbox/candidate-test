<?php

namespace Database\Factories;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayup>
 */
class CltLayupFactory extends Factory
{
    protected $model = CltLayup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Standard 3-Ply Wall',
            'Heavy Floor Panel',
            'Custom Span Beam',
            'Standard 3-Ply Floor',
            'Premium Roof Deck',
            'Structural Core Panel'
        ];

        return [
            'name' => $this->faker->randomElement($names),
        ];
    }
}
