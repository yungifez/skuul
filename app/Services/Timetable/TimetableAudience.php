<?php

namespace App\Services\Timetable;

use App\Enums\Role;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Decide which timetable records a person may read.
 *
 * Timetable records store a subject, not a course offering. The offering and
 * roster rules are therefore the source of truth when a learner or teacher
 * reads a timetable. An offering's own timetable reads that one offering.
 */
class TimetableAudience
{
    /**
     * Remove subject and role entries outside the viewer's audience.
     *
     * Planners keep the complete grid so they can build and review a schedule.
     * A learner or teacher only receives the records that apply to them.
     *
     * @param  Collection<string, TimetableRecord>  $records
     * @return Collection<string, TimetableRecord>
     */
    public function filter(Timetable $timetable, Collection $records, ?User $viewer): Collection
    {
        if ($viewer === null) {
            return $records;
        }

        $subjectIds = $this->subjectIdsFor($timetable, $records, $viewer);

        return $records
            ->filter(function (TimetableRecord $record) use ($subjectIds, $viewer): bool {
                if ($record->audience_role !== null && !$viewer->hasRole($record->audience_role)) {
                    return false;
                }

                if ($record->timetable_time_slot_weekdayable_type !== (new Subject)->getMorphClass()) {
                    return true;
                }

                return $subjectIds->contains((int) $record->timetable_time_slot_weekdayable_id);
            });
    }

    /**
     * Get the subjects of the timetable the viewer teaches or takes.
     *
     * Planners read every subject. A teacher reads the subjects of their
     * assignments, and a learner the subjects of the offerings they take.
     *
     * @param  Collection<string, TimetableRecord>  $records
     * @return Collection<int, int>
     */
    private function subjectIdsFor(Timetable $timetable, Collection $records, User $viewer): Collection
    {
        $subjectType = (new Subject)->getMorphClass();
        $subjectIds = $records
            ->where('timetable_time_slot_weekdayable_type', $subjectType)
            ->pluck('timetable_time_slot_weekdayable_id')
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();
        $period = $timetable->academicPeriod;

        if ($subjectIds->isEmpty() || $period === null) {
            return collect();
        }

        if ($viewer->hasRole(Role::Teacher)) {
            return $this->subjectsTaught($timetable, $period, $subjectIds, $viewer);
        }

        if ($viewer->hasRole(Role::Student)) {
            $student = $this->learnerOf($timetable, $period, $viewer);

            return $student === null ? collect() : $this->subjectsTaken($timetable, $period, $student, $subjectIds);
        }

        return $subjectIds;
    }

    /**
     * Get the viewer's enrollment that this timetable can serve.
     *
     * A section's timetable serves the learners of that section. Any other
     * timetable serves the learners of its campus.
     */
    private function learnerOf(Timetable $timetable, AcademicPeriod $period, User $viewer): ?StudentRecord
    {
        return StudentRecord::query()
            ->where('user_id', $viewer->id)
            ->where('school_id', $period->school_id)
            ->when(
                $timetable->academic_cycle_section_id !== null,
                fn (Builder $query): Builder => $query->where('academic_cycle_section_id', $timetable->academic_cycle_section_id),
            )
            ->attending()
            ->with('academicCycleSection.academicLevel')
            ->first();
    }

    /**
     * @param  Collection<int, int>  $subjectIds
     * @return Collection<int, int>
     */
    private function subjectsTaught(
        Timetable $timetable,
        AcademicPeriod $period,
        Collection $subjectIds,
        User $teacher,
    ): Collection {
        $section = $timetable->academicCycleSection;

        return TeachingAssignment::inSchool($period->school_id)
            ->where('academic_period_id', $period->id)
            ->forTeacher($teacher)
            ->whereIn('subject_id', $subjectIds)
            ->when($timetable->course_offering_id !== null, fn (Builder $query): Builder => $query
                ->where('course_offering_id', $timetable->course_offering_id))
            ->when($section !== null, fn (Builder $query): Builder => $query
                ->where(function (Builder $query) use ($section): void {
                    $query->where('academic_cycle_section_id', $section?->id)
                        ->orWhere(fn (Builder $query): Builder => $query
                            ->whereNull('academic_cycle_section_id')
                            ->whereIn('course_offering_id', CourseOffering::query()->reachingSection($section)->select('id')));
                }))
            ->pluck('subject_id')
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, int>  $subjectIds
     * @return Collection<int, int>
     */
    private function subjectsTaken(
        Timetable $timetable,
        AcademicPeriod $period,
        StudentRecord $student,
        Collection $subjectIds,
    ): Collection {
        return CourseOffering::inSchool($period->school_id)
            ->where('academic_period_id', $period->id)
            ->whereIn('subject_id', $subjectIds)
            ->when($timetable->course_offering_id !== null, fn (Builder $query): Builder => $query->whereKey($timetable->course_offering_id))
            ->takenBy($student)
            ->pluck('subject_id')
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();
    }
}
