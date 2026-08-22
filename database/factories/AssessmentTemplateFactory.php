<?php

namespace Database\Factories;

use App\Models\AssessmentTemplate;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTemplate>
 */
class AssessmentTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var School $school */
        $school = School::query()->first() ?? School::factory()->create();

        return [
            'school_id'   => $school->id,
            'name'        => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active'   => true,
        ];
    }
}
