<?php

namespace Database\Factories;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ReportCardSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportCardSnapshot>
 */
class ReportCardSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id'          => School::factory(),
            'student_record_id'  => StudentRecord::factory(),
            'academic_year_id'   => AcademicYear::factory(),
            'academic_period_id' => AcademicPeriod::factory(),
            'revision'           => 1,
            'average_percentage' => 75,
            'payload'            => ['results' => []],
            'published_at'       => now(),
        ];
    }
}
