<?php

namespace App\Services\Timetable;

use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\User;
use App\Models\Weekday;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Put together one person's week from every timetable that applies to them.
 *
 * A learner on a campus that teaches separate subjects moves between rooms:
 * their week is their section's timetable plus the own timetables of the
 * subjects they take. A teacher's week is every lesson they teach.
 */
class PersonalWeek
{
    public function __construct(private TimetableGrid $grid) {}

    /**
     * Get the published timetables that hold a learner's week.
     *
     * These are the learner's section timetable, the timetables of the
     * subjects they take that meet at their own times, and the schoolwide
     * ones, all at the learner's own campus.
     *
     * @return Collection<int, Timetable>
     */
    public function timetablesOfLearner(StudentRecord $learner): Collection
    {
        $learner->loadMissing('academicCycleSection.academicLevel');
        $sectionId = $learner->academic_cycle_section_id;

        $candidates = $this->publishedAt($learner->school_id)
            ->where(fn (Builder $owners): Builder => $owners
                ->where(fn (Builder $schoolwide): Builder => $schoolwide->whereNull('academic_cycle_section_id')->whereNull('course_offering_id'))
                ->when($sectionId !== null, fn (Builder $section): Builder => $section->orWhere('academic_cycle_section_id', $sectionId))
                ->orWhereIn('course_offering_id', CourseOffering::query()->takenBy($learner)->select('id')))
            ->get();

        return $this->currentOf($candidates);
    }

    /**
     * Get the published timetables where a teacher teaches at one campus.
     *
     * @return Collection<int, Timetable>
     */
    public function timetablesOfTeacher(User $teacher, int $schoolId): Collection
    {
        $assignments = TeachingAssignment::inSchool($schoolId)
            ->forTeacher($teacher)
            ->runningOn()
            ->with('courseOffering.academicLevel')
            ->get();

        if ($assignments->isEmpty()) {
            return new Collection;
        }

        $offeringIds = $assignments->pluck('course_offering_id')->unique()->values()->all();
        $sectionIds = $assignments
            ->flatMap(fn (TeachingAssignment $assignment): array => $this->sectionIdsReachedBy($assignment))
            ->unique()
            ->values()
            ->all();

        $candidates = $this->publishedAt($schoolId)
            ->where(fn (Builder $owners): Builder => $owners
                ->whereIn('course_offering_id', $offeringIds)
                ->orWhereIn('academic_cycle_section_id', $sectionIds))
            ->get();

        return $this->currentOf($candidates);
    }

    /**
     * Lay the lessons of several timetables out by day, earliest first.
     *
     * Each timetable is read as the viewer would read it, so a learner sees
     * only the lessons they take and a teacher only the lessons they teach.
     *
     * @param  Collection<int, Timetable>  $timetables
     * @return array<int, array{weekday: string, lessons: list<array{start: string, stop: string, name: string, kind: string, teachers: array<int, string>, timetable: string}>}>
     */
    public function of(Collection $timetables, ?User $viewer, bool $lessonsOnly = false): array
    {
        $days = Weekday::query()->orderBy('id')->get(['id', 'name'])
            ->mapWithKeys(fn (Weekday $weekday): array => [$weekday->id => ['weekday' => $weekday->name, 'lessons' => []]])
            ->all();

        foreach ($timetables as $timetable) {
            $grid = $this->grid->of($timetable, viewer: $viewer);
            $offeringName = $timetable->courseOffering?->nameWithSubject();

            foreach ($grid['rows'] as $row) {
                foreach ($row['cells'] as $weekdayId => $cell) {
                    if (!$cell['active'] || $cell['kind'] === null || ($lessonsOnly && $cell['kind'] !== 'subject') || !isset($days[$weekdayId])) {
                        continue;
                    }

                    $days[$weekdayId]['lessons'][] = [
                        'start' => $row['start'],
                        'stop' => $row['stop'],
                        'name' => $cell['kind'] === 'subject' && $offeringName !== null ? $offeringName : (string) $cell['name'],
                        'kind' => $cell['kind'],
                        'teachers' => $cell['teachers'],
                        'timetable' => $timetable->name,
                    ];
                }
            }
        }

        foreach ($days as $weekdayId => $day) {
            usort($day['lessons'], fn (array $one, array $other): int => [$one['start'], $one['stop']] <=> [$other['start'], $other['stop']]);
            $days[$weekdayId]['lessons'] = $day['lessons'];
        }

        return array_filter($days, fn (array $day): bool => $day['lessons'] !== []);
    }

    /**
     * Start a query for the published timetables of one campus.
     *
     * @return Builder<Timetable>
     */
    private function publishedAt(int $schoolId): Builder
    {
        return Timetable::query()
            ->published()
            ->with(['academicPeriod', 'courseOffering.subject'])
            ->whereHas('academicPeriod', fn (Builder $period): Builder => $period->where('school_id', $schoolId))
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /**
     * Keep the timetables in use today, one for each section, subject, or schoolwide schedule.
     *
     * When no term covers today, such as during a holiday, the newest
     * published timetables stand in.
     *
     * @param  Collection<int, Timetable>  $candidates
     * @return Collection<int, Timetable>
     */
    private function currentOf(Collection $candidates): Collection
    {
        $today = school_today();
        $current = $candidates->filter(fn (Timetable $timetable): bool => (bool) $timetable->academicPeriod?->covers($today));

        return ($current->isNotEmpty() ? $current : $candidates)
            ->unique(fn (Timetable $timetable): string => $timetable->course_offering_id !== null
                ? 'offering:'.$timetable->course_offering_id
                : 'section:'.($timetable->academic_cycle_section_id ?? 'schoolwide'))
            ->values();
    }

    /**
     * Get the sections whose timetables can hold a lesson of this assignment.
     *
     * @return array<int, int>
     */
    private function sectionIdsReachedBy(TeachingAssignment $assignment): array
    {
        if ($assignment->academic_cycle_section_id !== null) {
            return [$assignment->academic_cycle_section_id];
        }

        return $assignment->courseOffering?->reachedSectionIds() ?? [];
    }
}
