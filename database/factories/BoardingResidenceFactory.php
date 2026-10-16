<?php

namespace Database\Factories;

use App\Models\BoardingResidence;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardingResidence>
 */
class BoardingResidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->unique()->streetName().' Residence',
            'notes' => null,
        ];
    }
}
