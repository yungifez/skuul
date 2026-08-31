<?php

namespace App\Services\Timetable;

use App\Enums\RosterMode;
use App\Models\CustomTimetableItem;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\Weekday;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Read one timetable as the week people picture.
 *
 * Every screen that draws a timetable wants the same shape: a row for each
 * time slot, a column for each weekday, and a cell that already knows what
 * it holds and who takes it. Building that here keeps the grid, the builder,
 * and the printed sheet from drifting apart.
 */
class TimetableGrid
{
    /**
     * Build the whole week of one timetable.
     *
     * @return array{
     *     weekdays: array<int, array{id: int, name: string, short: string, used: bool, is_weekend: bool}>,
     *     rows: array<int, array{id: int, start: string, stop: string, recurrence: string, occurs_on: string|null, cells: array<int, array{active: bool, kind: string|null, name: string|null, teachers: array<int, string>, audience_role: string|null, recurrence: string, occurs_on: string|null}>}>,
     *     slot_count: int,
     *     filled_count: int,
     *     empty_count: int
     * }
     */
    public function of(Timetable $timetable): array
    {
        $weekdays = Weekday::query()->orderBy('id')->get(['id', 'name']);
        $slots = $timetable->timeSlots()->get()->sortBy('start_time')->values();
        $records = $this->recordsOf($slots->pluck('id')->all());
        $names = $this->namesOf($records);
        $teachers = $this->teachersOf($timetable, $records);

        $rows = [];
        $filled = 0;
        $used = [];

        foreach ($slots as $slot) {
            $cells = [];
            $oneTimeWeekdayId = $slot->recurrence === 'one_time' && $slot->occurs_on !== null
                ? $weekdays->firstWhere('name', Carbon::parse($slot->occurs_on)->englishDayOfWeek)?->id
                : null;

            foreach ($weekdays as $weekday) {
                $active = $slot->recurrence !== 'one_time' || $weekday->id === $oneTimeWeekdayId;
                $record = $records->get($slot->id.':'.$weekday->id);
                $cell = $this->cellOf($record, $names, $teachers);
                $cell['active'] = $active;
                $cell['recurrence'] = (string) $slot->recurrence;
                $cell['occurs_on'] = $slot->occurs_on?->toDateString();

                if ($active && $cell['kind'] !== null) {
                    $filled++;
                    $used[$weekday->id] = true;
                }

                $cells[$weekday->id] = $cell;
            }

            $rows[] = [
                'id' => $slot->id,
                'start' => (string) $slot->start_time,
                'stop' => (string) $slot->stop_time,
                'recurrence' => (string) $slot->recurrence,
                'occurs_on' => $slot->occurs_on?->toDateString(),
                'cells' => $cells,
            ];
        }

        $slotCount = $slots->count() * $weekdays->count();

        return [
            'weekdays' => $weekdays
                ->map(fn (Weekday $weekday): array => [
                    'id' => $weekday->id,
                    'name' => $weekday->name,
                    'short' => substr($weekday->name, 0, 3),
                    'used' => isset($used[$weekday->id]),
                    // A school that teaches at the weekend places lessons
                    // there, and only then does the day earn a column.
                    'is_weekend' => in_array($weekday->name, ['Saturday', 'Sunday'], true),
                ])
                ->all(),
            'rows' => $rows,
            'slot_count' => $slotCount,
            'filled_count' => $filled,
            'empty_count' => $slotCount - $filled,
        ];
    }

    /**
     * Read what one cell holds.
     *
     * @param  Collection<string, string>  $names
     * @param  Collection<int, array<int, string>>  $teachers
     * @return array{active?: bool, kind: string|null, name: string|null, teachers: array<int, string>, audience_role: string|null, recurrence?: string, occurs_on?: string|null}
     */
    private function cellOf(?TimetableRecord $record, Collection $names, Collection $teachers): array
    {
        if ($record === null) {
            return ['kind' => null, 'name' => null, 'teachers' => [], 'audience_role' => null];
        }

        $type = (string) $record->timetable_time_slot_weekdayable_type;
        $id = (int) $record->timetable_time_slot_weekdayable_id;
        $name = $names->get($type.':'.$id);

        if ($name === null) {
            // The subject or item was deleted after the lesson was placed.
            return ['kind' => null, 'name' => null, 'teachers' => [], 'audience_role' => null];
        }

        $isSubject = $type === (new Subject)->getMorphClass();

        return [
            'kind' => $isSubject ? 'subject' : 'break',
            'name' => $name,
            'teachers' => $isSubject ? $teachers->get($id, []) : [],
            'audience_role' => $record->audience_role,
        ];
    }

    /**
     * Read the placed lessons, keyed by the cell they sit in.
     *
     * @param  array<int, int>  $slotIds
     * @return Collection<string, TimetableRecord>
     */
    private function recordsOf(array $slotIds): Collection
    {
        if ($slotIds === []) {
            return collect();
        }

        return TimetableRecord::query()
            ->whereIn('timetable_time_slot_id', $slotIds)
            ->get()
            ->keyBy(fn (TimetableRecord $record): string => $record->timetable_time_slot_id.':'.$record->weekday_id);
    }

    /**
     * Read the name of everything the week holds, keyed by type and key.
     *
     * @param  Collection<string, TimetableRecord>  $records
     * @return Collection<string, string>
     */
    private function namesOf(Collection $records): Collection
    {
        $subjectType = (new Subject)->getMorphClass();
        $customType = (new CustomTimetableItem)->getMorphClass();
        $keysOf = fn (string $type): array => $records
            ->where('timetable_time_slot_weekdayable_type', $type)
            ->pluck('timetable_time_slot_weekdayable_id')
            ->unique()
            ->all();

        $names = collect();

        foreach (Subject::query()->whereKey($keysOf($subjectType))->get(['id', 'name']) as $subject) {
            $names->put($subjectType.':'.$subject->id, $subject->name);
        }

        foreach (CustomTimetableItem::query()->whereKey($keysOf($customType))->get(['id', 'name']) as $item) {
            $names->put($customType.':'.$item->id, $item->name);
        }

        return $names;
    }

    /**
     * Read who takes each subject of this timetable.
     *
     * @param  Collection<string, TimetableRecord>  $records
     * @return Collection<int, array<int, string>>
     */
    private function teachersOf(Timetable $timetable, Collection $records): Collection
    {
        $cycleSection = $timetable->academicCycleSection;
        $subjectIds = $records
            ->where('timetable_time_slot_weekdayable_type', (new Subject)->getMorphClass())
            ->pluck('timetable_time_slot_weekdayable_id')
            ->unique()
            ->all();

        if ($cycleSection === null || $subjectIds === []) {
            return $this->asTeacherLists([]);
        }

        $cycleSection->loadMissing('academicLevel');
        $levelScopeIds = $cycleSection->academicLevel->hierarchyIds();

        $assignments = TeachingAssignment::query()
            ->whereIn('subject_id', $subjectIds)
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

        $teachers = [];

        foreach ($assignments as $subjectId => $group) {
            $teachers[(int) $subjectId] = $group
                ->map(fn (TeachingAssignment $assignment): ?string => $assignment->teacher?->name)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return $this->asTeacherLists($teachers);
    }

    /**
     * Wrap the teacher lists, so the collection carries the declared types.
     *
     * @param  array<int, array<int, string>>  $teachers
     * @return Collection<int, array<int, string>>
     */
    private function asTeacherLists(array $teachers): Collection
    {
        return collect($teachers);
    }
}
