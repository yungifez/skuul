<?php

namespace Database\Factories;

use App\Enums\TopicCoverageStatus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyllabusTopicCoverage>
 */
class SyllabusTopicCoverageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'syllabus_topic_id' => SyllabusTopic::factory(),
            'academic_cycle_section_id' => null,
            'status' => TopicCoverageStatus::Covered,
            'covered_on' => now()->toDateString(),
        ];
    }
}
