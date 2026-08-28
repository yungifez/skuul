<?php

namespace App\Services\Timetable;

use App\Enums\RosterMode;
use App\Models\Facility;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Find the clashes that stop a timetable from being published.
 *
 * Two lessons in one room, one teacher in two rooms, or one section in two
 * lessons at the same time are all mistakes that only show up when the whole
 * period is read together.
 */
class TimetableConflictChecker
{
    /**
     * Get every conflict in the timetable, as sentences people can act on.
     *
     * @return array<int, string>
     */
    public function conflicts(Timetable $timetable): array
    {
        return array_merge(
            $this->overlappingSlots($timetable),
            $this->teacherClashes($timetable),
            $this->roomClashes($timetable),
        );
    }

    /**
     * Find time slots of one timetable that cover the same minutes.
     *
     * @return array<int, string>
     */
    private function overlappingSlots(Timetable $timetable): array
    {
        $slots = $timetable->timeSlots()->get(['id', 'start_time', 'stop_time'])->values();
        $conflicts = [];

        foreach ($slots as $index => $slot) {
            foreach ($slots->slice($index + 1) as $other) {
                if ($this->overlaps($slot->start_time, $slot->stop_time, $other->start_time, $other->stop_time)) {
                    $conflicts[] = "The time slots $slot->start_time-$slot->stop_time and $other->start_time-$other->stop_time overlap.";
                }
            }
        }

        return $conflicts;
    }

    /**
     * Find teachers who would stand in two places at once.
     *
     * @return array<int, string>
     */
    private function teacherClashes(Timetable $timetable): array
    {
        $entries = $this->entriesOf($timetable);

        if ($entries->isEmpty()) {
            return [];
        }

        $published = Timetable::query()
            ->published()
            ->where('academic_period_id', $timetable->academic_period_id)
            ->whereKeyNot($timetable->getKey())
            ->get();

        $conflicts = [];

        foreach ($published as $other) {
            foreach ($this->entriesOf($other) as $otherEntry) {
                foreach ($entries as $entry) {
                    if ($entry['weekday_id'] !== $otherEntry['weekday_id']) {
                        continue;
                    }

                    if (!$this->overlaps($entry['start_time'], $entry['stop_time'], $otherEntry['start_time'], $otherEntry['stop_time'])) {
                        continue;
                    }

                    $shared = array_intersect($entry['teacher_ids'], $otherEntry['teacher_ids']);

                    foreach ($shared as $teacherId) {
                        $name = $entry['teacher_names'][$teacherId] ?? "Teacher $teacherId";
                        $conflicts[] = "$name already teaches $other->name at {$entry['start_time']} on that day.";
                    }
                }
            }
        }

        return array_values(array_unique($conflicts));
    }

    /**
     * Find sections that have claimed the same place at the same time.
     *
     * A place is the shared hall or laboratory a lesson was moved into, or,
     * when it was not moved, the section's own room. A section room is
     * optional, so schedules without one remain valid: a school that uses one
     * stable homeroom per section gains room protection without configuring
     * a facilities catalogue first.
     *
     * @return array<int, string>
     */
    private function roomClashes(Timetable $timetable): array
    {
        $entries = $this->entriesOf($timetable);

        if ($entries->isEmpty()) {
            return [];
        }

        $published = Timetable::query()
            ->published()
            ->where('academic_period_id', $timetable->academic_period_id)
            ->whereKeyNot($timetable->getKey())
            ->get();
        $conflicts = [];

        foreach ($published as $other) {
            foreach ($this->entriesOf($other) as $otherEntry) {
                foreach ($entries as $entry) {
                    if ($entry['room'] === null || $entry['room'] !== $otherEntry['room'] || $entry['weekday_id'] !== $otherEntry['weekday_id']) {
                        continue;
                    }

                    if ($this->overlaps($entry['start_time'], $entry['stop_time'], $otherEntry['start_time'], $otherEntry['stop_time'])) {
                        $conflicts[] = "{$entry['room']} is already in use by $other->name at {$entry['start_time']} on that day.";
                    }
                }
            }
        }

        return array_values(array_unique($conflicts));
    }

    /**
     * Read the lessons of a timetable with the teachers who take them.
     */
    private function entriesOf(Timetable $timetable): Collection
    {
        $records = TimetableRecord::query()
            ->whereIn('timetable_time_slot_id', $timetable->timeSlots()->pluck('id'))
            ->get();

        $slots = $timetable->timeSlots()->get()->keyBy('id');
        $subjectMorphClass = (new Subject)->getMorphClass();
        $subjects = Subject::query()
            ->whereKey(
                $records
                    ->where('timetable_time_slot_weekdayable_type', $subjectMorphClass)
                    ->pluck('timetable_time_slot_weekdayable_id')
                    ->unique()
            )
            ->get()
            ->keyBy('id');
        $cycleSection = $timetable->academicCycleSection;
        $cycleSection?->loadMissing('academicLevel');
        $levelScopeIds = $cycleSection?->academicLevel?->hierarchyIds() ?? [];
        $facilityNames = Facility::query()
            ->whereKey($records->pluck('facility_id')->filter()->unique())
            ->pluck('name', 'id');
        $assignmentsBySubject = $cycleSection === null || $subjects->isEmpty()
            ? collect()
            : TeachingAssignment::query()
                ->whereIn('subject_id', $subjects->keys())
                ->where('academic_period_id', $timetable->academic_period_id)
                ->runningOn()
                ->whereHas('courseOffering', function ($query) use ($cycleSection, $levelScopeIds): void {
                    $query->where(function ($offerings) use ($cycleSection, $levelScopeIds): void {
                        $offerings->whereHas('cycleSections', fn ($sections) => $sections->whereKey($cycleSection->id))
                            ->orWhere(function ($offerings) use ($levelScopeIds): void {
                                $offerings->where('roster_mode', RosterMode::AcademicLevel)
                                    ->whereIn('academic_level_id', $levelScopeIds);
                            });
                    });
                })
                ->with('teacher:id,name')
                ->get()
                ->groupBy('subject_id');

        /** @var Collection<int, array{weekday_id: int, start_time: string, stop_time: string, room: string|null, teacher_ids: array<int, int>, teacher_names: array<int|string, string>}> $entries */
        $entries = $records->map(function (TimetableRecord $record) use ($assignmentsBySubject, $facilityNames, $slots, $subjectMorphClass, $subjects, $timetable): ?array {
            $slot = $slots->get($record->timetable_time_slot_id);
            $subject = $record->timetable_time_slot_weekdayable_type === $subjectMorphClass
                ? $subjects->get($record->timetable_time_slot_weekdayable_id)
                : null;

            if ($slot === null || !$subject instanceof Subject) {
                return null;
            }

            $teachers = $assignmentsBySubject->get($subject->id, collect())
                ->map(fn (TeachingAssignment $assignment): ?\App\Models\User => $assignment->teacher)
                ->filter();

            return [
                'weekday_id' => (int) $record->weekday_id,
                'start_time' => (string) $slot->start_time,
                'stop_time' => (string) $slot->stop_time,
                // A lesson moved into a shared place claims that place. Every
                // other lesson keeps the section's own room.
                'room' => $record->facility_id !== null
                    ? $facilityNames->get($record->facility_id)
                    : (filled($timetable->academicCycleSection?->room) ? $timetable->academicCycleSection->room : null),
                'teacher_ids' => $teachers->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                'teacher_names' => $teachers->pluck('name', 'id')->all(),
            ];
        })->filter()->values();

        return $entries
            ->map(fn (array $entry): array => $entry)
            ->values();
    }

    /**
     * Check if two time ranges cover any of the same minutes.
     */
    private function overlaps(string $start, string $stop, string $otherStart, string $otherStop): bool
    {
        $start = Carbon::parse($start);
        $stop = Carbon::parse($stop);
        $otherStart = Carbon::parse($otherStart);
        $otherStop = Carbon::parse($otherStop);

        return $start->lessThan($otherStop) && $stop->greaterThan($otherStart);
    }
}
