<?php

namespace App\Services\Timetable;

use App\Models\CustomTimetableItem;
use App\Models\Subject;
use App\Models\TimetableTimeSlot;

class TimeSlotService
{
    /**
     * The kinds of thing a cell of the week can hold.
     *
     * The forms and the builder both send these keys, and only this map turns
     * one into the class the pivot stores.
     *
     * @return array<string, string>
     */
    public static function recordableTypes(): array
    {
        return [
            'subject' => (new Subject)->getMorphClass(),
            'customTimetableItem' => (new CustomTimetableItem)->getMorphClass(),
        ];
    }

    /**
     * Create timetable time slot.
     *
     * @param  array<string, mixed>  $data
     */
    public function createTimeSlot(array $data): TimetableTimeSlot
    {
        return TimetableTimeSlot::create([
            'start_time' => $data['start_time'],
            'stop_time' => $data['stop_time'],
            'timetable_id' => $data['timetable_id'],
            'recurrence' => $data['recurrence'] ?? 'weekly',
            'occurs_on' => $data['occurs_on'] ?? null,
        ]);
    }

    /**
     * Delete Timetable.
     */
    public function deleteTimeSlot(TimetableTimeSlot $timeSlot): void
    {
        $timeSlot->delete();
    }

    /**
     * Create timetable time record.
     *
     * A request with no chosen item empties the cell instead of filling it.
     *
     * @param  array<string, mixed>  $data
     */
    public function createTimetableRecord(TimetableTimeSlot $timeSlot, array $data): void
    {
        $recordableId = $data['id'] ?? null;

        if ($recordableId === null || $recordableId === '') {
            $this->clearRecord($timeSlot, (int) $data['weekday_id']);

            return;
        }

        $this->placeRecord(
            $timeSlot,
            (int) $data['weekday_id'],
            (string) $data['type'],
            (int) $recordableId,
            isset($data['facility_id']) ? (int) $data['facility_id'] : null,
        );
    }

    /**
     * Put one subject or custom item in one cell of the week.
     *
     * A cell holds one thing, so whatever was there is detached first.
     */
    public function placeRecord(
        TimetableTimeSlot $timeSlot,
        int $weekdayId,
        string $kind,
        int $recordableId,
        ?int $facilityId = null,
        ?string $audienceRole = null,
    ): void {
        $type = self::recordableTypes()[$kind] ?? null;

        if ($type === null) {
            return;
        }

        $timeSlot->weekdays()->detach($weekdayId);
        $timeSlot->weekdays()->attach($weekdayId, [
            'timetable_time_slot_weekdayable_id' => $recordableId,
            'timetable_time_slot_weekdayable_type' => $type,
            'audience_role' => $audienceRole,

            // A lesson can be moved out of the section's own room for this
            // one entry. Publication then checks that place like any other.
            'facility_id' => $facilityId,
        ]);
    }

    /**
     * Empty one cell of the week.
     */
    public function clearRecord(TimetableTimeSlot $timeSlot, int $weekdayId): void
    {
        $timeSlot->weekdays()->detach($weekdayId);
    }
}
