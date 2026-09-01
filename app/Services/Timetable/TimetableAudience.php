<?php

namespace App\Services\Timetable;

use App\Enums\Role;
use App\Enums\RosterMode;
use App\Models\AcademicCycleSection;
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
 * reads a section timetable.
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

        if ($subjectIds->isEmpty() || $timetable->academic_cycle_section_id === null) {
            return collect();
        }

        $section = $timetable->academicCycleSection;
        $period = $timetable->academicPeriod;

        if ($section === null || $period === null) {
            return collect();
        }

        if ($viewer->hasRole(Role::Teacher)) {
            return $this->subjectsTaughtInSection($period, $section, $subjectIds, $viewer);
        }

        if ($viewer->hasRole(Role::Student)) {
            $student = $viewer->studentRecord()->attending()->first();

            if ($student === null || $student->academic_cycle_section_id !== $section->id) {
                return collect();
            }

            return $this->subjectsTakenInSection($period, $section, $student, $subjectIds);
        }

        return $subjectIds;
    }

    /**
     * @param  Collection<int, int>  $subjectIds
     * @return Collection<int, int>
     */
    private function subjectsTaughtInSection(
        AcademicPeriod $period,
        AcademicCycleSection $section,
        Collection $subjectIds,
        User $teacher,
    ): Collection {
        return TeachingAssignment::inSchool()
            ->where('academic_period_id', $period->id)
            ->forTeacher($teacher)
            ->whereIn('subject_id', $subjectIds)
            ->where(function (Builder $query) use ($section): void {
                $query->where('academic_cycle_section_id', $section->id)
                    ->orWhere(function (Builder $query) use ($section): void {
                        $query->whereNull('academic_cycle_section_id')
                            ->whereHas('courseOffering', fn (Builder $offering): Builder => $this->offeringCoversSection($offering, $section));
                    });
            })
            ->pluck('subject_id')
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, int>  $subjectIds
     * @return Collection<int, int>
     */
    private function subjectsTakenInSection(
        AcademicPeriod $period,
        AcademicCycleSection $section,
        StudentRecord $student,
        Collection $subjectIds,
    ): Collection {
        return CourseOffering::inSchool()
            ->where('academic_period_id', $period->id)
            ->whereIn('subject_id', $subjectIds)
            ->where(function (Builder $query) use ($section, $student): void {
                $query->where(function (Builder $query) use ($section): void {
                    $query->whereIn('roster_mode', [RosterMode::HomeSection->value, RosterMode::CombinedHomeSections->value])
                        ->whereHas('cycleSections', fn (Builder $sections): Builder => $sections->whereKey($section->id));
                })->orWhere(function (Builder $query) use ($section): void {
                    $query->where('roster_mode', RosterMode::AcademicLevel->value)
                        ->where('academic_level_id', $section->academic_level_id);
                })->orWhere(function (Builder $query) use ($student): void {
                    $query->where('roster_mode', RosterMode::IndividualRoster->value)
                        ->whereHas('studentRecords', fn (Builder $students): Builder => $students->whereKey($student->id));
                });
            })
            ->pluck('subject_id')
            ->map(fn (int|string $subjectId): int => (int) $subjectId)
            ->unique()
            ->values();
    }

    private function offeringCoversSection(Builder $query, AcademicCycleSection $section): Builder
    {
        return $query->where(function (Builder $query) use ($section): void {
            $query->where(function (Builder $query) use ($section): void {
                $query->whereIn('roster_mode', [RosterMode::HomeSection->value, RosterMode::CombinedHomeSections->value])
                    ->whereHas('cycleSections', fn (Builder $sections): Builder => $sections->whereKey($section->id));
            })->orWhere(function (Builder $query) use ($section): void {
                $query->where('roster_mode', RosterMode::AcademicLevel->value)
                    ->where('academic_level_id', $section->academic_level_id);
            })->orWhereHas('studentRecords', fn (Builder $students): Builder => $students
                ->where('academic_cycle_section_id', $section->id));
        });
    }
}
