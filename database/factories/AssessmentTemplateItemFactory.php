<?php

namespace Database\Factories;

use App\Enums\GradeItemType;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTemplateItem>
 */
class AssessmentTemplateItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_template_id' => AssessmentTemplate::factory(),
            'name' => fake()->words(2, true),
            'type' => GradeItemType::Numeric,
            'max_points' => 100,
            'weight' => 1,
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
