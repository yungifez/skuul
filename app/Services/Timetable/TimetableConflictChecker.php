<?php

namespace App\Services\Timetable;

use App\Models\Subject;
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
     * Read the lessons of a timetable with the teachers who take them.
     *
     * @return Collection<int, array{weekday_id: int, start_time: string, stop_time: string, teacher_ids: array<int, int>, teacher_names: array<int, string>}>
     */
    private function entriesOf(Timetable $timetable): Collection
    {
        $records = TimetableRecord::query()
            ->whereIn('timetable_time_slot_id', $timetable->timeSlots()->pluck('id'))
            ->get();

        $slots = $timetable->timeSlots()->get()->keyBy('id');

        /** @var Collection<int, array{weekday_id: int, start_time: string, stop_time: string, teacher_ids: array<int, int>, teacher_names: array<int, string>}> $entries */
        $entries = $records->map(function (TimetableRecord $record) use ($slots): ?array {
            $slot = $slots->get($record->timetable_time_slot_id);
            $subject = $record->timetableRecordable()->first();

            if ($slot === null || !$subject instanceof Subject) {
                return null;
            }

            $teachers = $subject->teachers()->get(['users.id', 'users.name']);

            return [
                'weekday_id' => (int) $record->weekday_id,
                'start_time' => (string) $slot->start_time,
                'stop_time' => (string) $slot->stop_time,
                'teacher_ids' => $teachers->pluck('id')->map(fn ($id): int => (int) $id)->all(),
                'teacher_names' => $teachers->pluck('name', 'id')->all(),
            ];
        })->filter()->values();

        return $entries;
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
