<?php

namespace App\Services\Timetable;

use App\Models\CustomTimetableItem;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Services\Print\PrintService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TimetableService
{
    /**
     * Get timetables for one academic period and one exact home group.
     *
     * @return Collection<int, Timetable>
     */
    public function getAllTimetablesInAcademicPeriodAndCycleSection(int $academicPeriodId, int $academicCycleSectionId): Collection
    {
        return Timetable::query()
            ->where('academic_period_id', $academicPeriodId)
            ->where('academic_cycle_section_id', $academicCycleSectionId)
            ->get();
    }

    /**
     * Create timetable.
     *
     * @param  array{name: string, description?: string|null, academic_cycle_section_id?: int|null, academic_period_id: int}  $data
     */
    public function createTimetable(array $data): Timetable
    {
        return Timetable::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'academic_cycle_section_id' => $data['academic_cycle_section_id'] ?? null,
            'academic_period_id' => $data['academic_period_id'],
        ]);
    }

    /**
     * Create a timetable and its calendar entries together.
     *
     * @param  array{name: string, description?: string|null, academic_cycle_section_id?: int|null, academic_period_id: int}  $data
     * @param  array<int, array{weekday_id: int, start_time: string, stop_time: string, recurrence: string, occurs_on?: string|null, starts_on?: string|null, recurrence_interval?: int, recurrence_weekdays?: array<int, int>|null, type: string, subject_id?: int|null, title?: string|null, audience_role?: string|null}>  $events
     */
    public function createTimetableWithEvents(array $data, array $events): Timetable
    {
        return DB::transaction(function () use ($data, $events): Timetable {
            $timetable = $this->createTimetable($data);
            $timeSlots = app(TimeSlotService::class);

            foreach ($events as $event) {
                $slot = TimetableTimeSlot::firstOrCreate([
                    'timetable_id' => $timetable->id,
                    'start_time' => $event['start_time'],
                    'stop_time' => $event['stop_time'],
                    'recurrence' => $event['recurrence'],
                    'occurs_on' => $event['occurs_on'] ?? null,
                    'starts_on' => $event['starts_on'] ?? null,
                    'recurrence_interval' => $event['recurrence_interval'] ?? 1,
                ]);

                $ruleWeekdays = array_values(array_unique(array_map('intval', $event['recurrence_weekdays'] ?? [])));

                if ($ruleWeekdays !== []) {
                    $slotWeekdays = array_values(array_unique(array_merge($slot->recurrence_weekdays ?? [], $ruleWeekdays)));

                    if ($slotWeekdays !== ($slot->recurrence_weekdays ?? [])) {
                        $slot->update(['recurrence_weekdays' => $slotWeekdays]);
                    }
                }

                $recordableId = $event['subject_id'] ?? null;

                if ($event['type'] !== 'subject') {
                    $recordableId = CustomTimetableItem::firstOrCreate([
                        'school_id' => current_school_id(),
                        'name' => $event['title'],
                    ])->id;
                }

                $timeSlots->placeRecord(
                    $slot,
                    $event['weekday_id'],
                    $event['type'] === 'subject' ? 'subject' : 'customTimetableItem',
                    (int) $recordableId,
                    null,
                    $event['audience_role'] ?? null,
                );
            }

            return $timetable;
        });
    }

    /**
     * Update timetable.
     *
     * @param  mixed  $data
     * @return void
     */
    public function updateTimetable(Timetable $timetable, $data)
    {
        $timetable->name = $data['name'];
        $timetable->description = $data['description'];
        $timetable->save();
    }

    /**
     * Print timetable.
     *
     *
     * @return Response
     */
    public function printTimetable(string $name, string $view, array $data)
    {
        return PrintService::page($view, $data);
    }

    /**
     * Delete timetable.
     *
     *
     * @return void
     */
    public function deleteTimetable(Timetable $timetable)
    {
        $timetable->delete();
    }

    /**
     * Get all custom timetable items in school.
     *
     * @return Collection
     */
    public function getAllCustomTimetableItem()
    {
        return CustomTimetableItem::inSchool()->get();
    }

    /**
     * Create custom timetable item.
     *
     * @param  array<mixed>  $record
     * @return CustomTimetableItem
     */
    public function createCustomTimetableItem($record)
    {
        return CustomTimetableItem::create([
            'name' => $record['name'],
            'school_id' => $record['school_id'],
        ]);
    }

    /**
     * Update a given custom timetable item.
     *
     * @param  array<mixed>  $record
     * @return CustomTimetableItem
     */
    public function updateCustomTimetableItem(CustomTimetableItem $customTimetableItem, $record)
    {
        $customTimetableItem->name = $record['name'];
        $customTimetableItem->save();

        return $customTimetableItem;
    }

    public function deleteCustomTimetableItem(CustomTimetableItem $customTimetableItem)
    {
        $customTimetableItem->timetableRecord()->delete();
        $customTimetableItem->delete();
    }
}
