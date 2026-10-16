<?php

namespace Database\Factories;

use App\Models\Dormitory;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dormitory>
 */
class DormitoryFactory extends Factory
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
            'name' => fake()->unique()->lastName().' House',
            'label' => 'House',
        ];
    }
}
