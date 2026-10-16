<?php

namespace Database\Factories;

use App\Enums\FacilityKind;
use App\Models\Facility;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::first()?->id ?? School::factory(),
            'name' => ucfirst(fake()->unique()->word()).' Hall',
            'kind' => FacilityKind::Hall,
            'capacity' => fake()->numberBetween(20, 400),
        ];
    }
}
