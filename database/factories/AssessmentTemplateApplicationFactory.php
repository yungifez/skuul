<?php

namespace Database\Factories;

use App\Models\AssessmentTemplate;
use App\Models\AssessmentTemplateApplication;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentTemplateApplication>
 */
class AssessmentTemplateApplicationFactory extends Factory
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
            'course_offering_id'     => CourseOffering::factory(),
            'applied_at'             => now(),
        ];
    }
}
