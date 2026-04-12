<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'primary_contact' => $this->faker->email(),
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'material_certifications' => $this->faker->randomElement(['SPF No. 1/2', 'D. Fir-L', 'C24', 'C16']),
            'last_audit_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}