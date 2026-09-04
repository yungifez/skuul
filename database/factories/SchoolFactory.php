<?php

namespace Database\Factories;

use App\Models\FinancialPeriod;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (School $school): void {
            FinancialPeriod::query()->firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => 'Current finance period',
                ],
                [
                    'starts_on' => now()->startOfYear()->toDateString(),
                    'ends_on' => now()->endOfYear()->toDateString(),
                ],
            );
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'initials' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->randomNumber(5),
        ];
    }

    public function createDefaultSchool()
    {
        return $this->state(function (array $attributes) {
            return [
                'id' => 1,
                'name' => 'Default school',
                'address' => $this->faker->address(),
                'initials' => $this->faker->unique()->word(),
                'code' => $this->faker->unique()->randomNumber(5),
            ];
        });
    }
}
