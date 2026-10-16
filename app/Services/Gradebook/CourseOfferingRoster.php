<?php

namespace App\Services\Gradebook;

use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CourseOfferingRoster
{
    /**
     * Refuse a grade or published result for a learner outside the offering.
     */
    public function ensureIncludes(CourseOffering $courseOffering, StudentRecord $enrollment): void
    {
        if ($this->includes($courseOffering, $enrollment)) {
            return;
        }

        if ($enrollment->school_id !== null && $enrollment->school_id !== $courseOffering->school_id) {
            throw new InvalidValueException('This student is enrolled in another school.');
        }

        throw new InvalidValueException('This student is not enrolled in the course offering.');
    }

    /**
     * Decide eligibility from the offering's declared roster mode.
     *
     * A learner the offering already marked stays on it after moving section
     * or campus, so the teacher can finish and publish the term they taught.
     */
    public function includes(CourseOffering $courseOffering, StudentRecord $enrollment): bool
    {
        if ($this->wasMarkedIn($courseOffering)->where('student_record_id', $enrollment->id)->exists()) {
            return true;
        }

        if ($enrollment->school_id !== $courseOffering->school_id) {
            return false;
        }

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

        return StudentRecord::query()
            ->enrolled()
            ->where(fn (Builder $roster) => $roster
                ->where(fn (Builder $current) => $this->currentRoster($current->inSchool($courseOffering->school_id), $courseOffering))
                ->orWhereIn('id', $this->wasMarkedIn($courseOffering)->select('student_record_id')))
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get();
    }

    /**
     * Limit a query to the learners the roster mode names today.
     *
     * @param  Builder<StudentRecord>  $query
     * @return Builder<StudentRecord>
     */
    private function currentRoster(Builder $query, CourseOffering $courseOffering): Builder
    {
        return match ($courseOffering->roster_mode) {
            RosterMode::HomeSection, RosterMode::CombinedHomeSections => $query
                ->whereIn('academic_cycle_section_id', $courseOffering->cycleSections->modelKeys()),
            RosterMode::AcademicLevel => $query
                ->whereHas('academicCycleSection', fn ($sections) => $sections->whereIn('academic_level_id', $courseOffering->academicLevel->teachingScopeIds())),
            RosterMode::IndividualRoster => $query
                ->whereIn('id', $courseOffering->studentRecords->modelKeys()),
        };
    }

    /**
     * Get the grades recorded in the offering.
     *
     * @return Builder<GradeEntry>
     */
    private function wasMarkedIn(CourseOffering $courseOffering): Builder
    {
        return GradeEntry::query()
            ->whereIn('grade_item_id', GradeItem::query()->forCourseOffering($courseOffering)->select('id'));
    }
}
