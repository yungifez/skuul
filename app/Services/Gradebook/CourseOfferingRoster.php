<?php

namespace App\Services\Gradebook;

use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use Illuminate\Support\Collection;

class CourseOfferingRoster
{
    /**
     * Refuse a grade or published result for a learner outside the offering.
     */
    public function ensureIncludes(CourseOffering $courseOffering, StudentRecord $enrollment): void
    {
        if ($enrollment->school_id !== null && $enrollment->school_id !== $courseOffering->school_id) {
            throw new InvalidValueException('This student is enrolled in another school.');
        }

        if (!$this->includes($courseOffering, $enrollment)) {
            throw new InvalidValueException('This student is not enrolled in the course offering.');
        }
    }

    /**
     * Decide eligibility from the offering's declared roster mode.
     */
    public function includes(CourseOffering $courseOffering, StudentRecord $enrollment): bool
    {
        $courseOffering->loadMissing(['academicLevel', 'cycleSections', 'studentRecords']);
        $enrollment->loadMissing('academicCycleSection');

        return match ($courseOffering->roster_mode) {
            RosterMode::HomeSection, RosterMode::CombinedHomeSections => $courseOffering->cycleSections
                ->contains('id', $enrollment->academic_cycle_section_id),
            RosterMode::AcademicLevel => in_array(
                $enrollment->academicCycleSection?->academic_level_id,
                $courseOffering->academicLevel->teachingScopeIds(),
                true,
            ),
            RosterMode::IndividualRoster => $courseOffering->studentRecords->contains('id', $enrollment->id),
        };
    }

    /**
     * Get the learners who belong in the offering's gradebook.
     *
     * @return Collection<int, StudentRecord>
     */
    public function students(CourseOffering $courseOffering): Collection
    {
        $courseOffering->loadMissing(['academicLevel', 'cycleSections', 'studentRecords']);

        $students = match ($courseOffering->roster_mode) {
            RosterMode::HomeSection, RosterMode::CombinedHomeSections => StudentRecord::query()
                ->inSchool($courseOffering->school_id)
                ->attending()
                ->whereIn('academic_cycle_section_id', $courseOffering->cycleSections->modelKeys()),
            RosterMode::AcademicLevel => StudentRecord::query()
                ->inSchool($courseOffering->school_id)
                ->attending()
                ->whereHas('academicCycleSection', fn ($query) => $query->whereIn('academic_level_id', $courseOffering->academicLevel->teachingScopeIds())),
            RosterMode::IndividualRoster => $courseOffering->studentRecords()->attending(),
        };

        return $students
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get();
    }
}
