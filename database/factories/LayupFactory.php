<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Layup;

class LayupFactory extends Factory
{
    protected $model = Layup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Standard 3-Ply Wall',
                'Heavy Floor Panel',
                'Custom Span Beam',
                'Standard 3-Ply Floor',
            ]),
        ];
    }
}
