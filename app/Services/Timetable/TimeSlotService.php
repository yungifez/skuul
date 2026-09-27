<?php

namespace App\Services\Timetable;

use App\Exceptions\InvalidValueException;
use App\Models\CustomTimetableItem;
use App\Models\Facility;
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
            'starts_on' => $data['starts_on'] ?? null,
            'recurrence_interval' => $data['recurrence_interval'] ?? 1,
            'recurrence_weekdays' => $data['recurrence_weekdays'] ?? null,
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

        $this->failIfNotOfTheTimetablesSchool($timeSlot, $kind, $recordableId, $facilityId);

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
     * Refuse a subject, item, or room that another school owns.
     *
     * The builder lists only this school's choices, but the id arrives from the
     * browser. A room must also be open and able to hold a lesson.
     *
     * @throws InvalidValueException
     */
    private function failIfNotOfTheTimetablesSchool(TimetableTimeSlot $timeSlot, string $kind, int $recordableId, ?int $facilityId): void
    {
        $schoolId = $timeSlot->timetable->academicPeriod->school_id;
        $recordable = $kind === 'subject' ? Subject::query() : CustomTimetableItem::query();

        if (!$recordable->whereKey($recordableId)->where('school_id', $schoolId)->exists()) {
            throw new InvalidValueException($kind === 'subject'
                ? 'Choose a subject this school teaches.'
                : 'Choose an item this school set up.');
        }

        if ($facilityId !== null && !Facility::query()->whereKey($facilityId)->where('school_id', $schoolId)->active()->holdsLessons()->exists()) {
            throw new InvalidValueException('Choose an open room of this school that can hold a lesson.');
        }
    }

    /**
     * Empty one cell of the week.
     */
    public function clearRecord(TimetableTimeSlot $timeSlot, int $weekdayId): void
    {
        $timeSlot->weekdays()->detach($weekdayId);
    }
}
