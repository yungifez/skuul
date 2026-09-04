<?php

namespace Database\Seeders;

use App\Enums\AcademicPeriodStatus;
use App\Models\CourseOffering;
use App\Models\School;
use App\Models\Syllabus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SyllabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::query()->first();

        if ($school === null) {
            return;
        }

        CourseOffering::query()
            ->where('school_id', $school->id)
            ->whereHas('academicPeriod', function (Builder $query): void {
                $query->whereNotIn('status', [
                    AcademicPeriodStatus::Closed->value,
                    AcademicPeriodStatus::Archived->value,
                ]);
            })
            ->orderBy('id')
            ->limit(10)
            ->get()
            ->values()
            ->each(function (CourseOffering $courseOffering, int $index): void {
                $filePath = 'pdfs/demo-syllabus-'.$courseOffering->id.'.pdf';
                Storage::disk('public')->put($filePath, "%PDF-1.4\nDemo syllabus\n");

                Syllabus::query()->firstOrCreate(
                    ['course_offering_id' => $courseOffering->id],
                    [
                        'name' => 'Demo syllabus '.($index + 1),
                        'description' => 'Course plan and learning outcomes for the simulated school.',
                        'file' => $filePath,
                    ],
                );
            });
    }
}
