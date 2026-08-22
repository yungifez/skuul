<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'    => $this->faker->company(),
            'code'    => $this->faker->unique()->bothify('ORG-#####'),
            'address' => $this->faker->address(),
            'email'   => $this->faker->companyEmail(),
            'phone'   => $this->faker->phoneNumber(),
        ];
    }
}
