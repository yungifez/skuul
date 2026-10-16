<?php

namespace Database\Factories;

use App\Enums\GradeAggregation;
use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTemplateCategory>
 */
class AssessmentTemplateCategoryFactory extends Factory
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
            'name' => fake()->unique()->words(2, true),
            'aggregation' => GradeAggregation::WeightedMean,
            'weight' => 1,
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
