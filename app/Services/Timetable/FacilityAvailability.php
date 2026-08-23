<?php

namespace App\Services\Timetable;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use Illuminate\Support\Carbon;

/**
 * Whether a shared thing is free at a given time.
 *
 * Two answers matter, and both have to agree: nobody else has booked it, and
 * no published lesson has been moved into it.
 */
class FacilityAvailability
{
    /**
     * Get the reasons the thing is not free, in words.
     *
     * An empty list means it is free.
     *
     * @return array<int, string>
     */
    public function clashesFor(Facility $facility, Carbon $from, Carbon $to, ?int $ignoreBookingId = null): array
    {
        $clashes = [];

        $booked = FacilityBooking::query()
            ->where('facility_id', $facility->id)
            ->running()
            ->overlapping($from->toDateTimeString(), $to->toDateTimeString())
            ->when($ignoreBookingId !== null, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->with('bookedBy')
            ->get();

        foreach ($booked as $booking) {
            $clashes[] = sprintf(
                '%s is already booked from %s to %s for %s.',
                $facility->name,
                $booking->starts_at->format('j M, H:i'),
                $booking->ends_at->format('H:i'),
                $booking->purpose,
            );
        }

        foreach ($this->lessonsIn($facility, $from, $to) as $lesson) {
            $clashes[] = $lesson;
        }

        return array_values(array_unique($clashes));
    }

    /**
     * Check whether the thing is free for the whole stretch.
     */
    public function isFree(Facility $facility, Carbon $from, Carbon $to, ?int $ignoreBookingId = null): bool
    {
        return $this->clashesFor($facility, $from, $to, $ignoreBookingId) === [];
    }

    /**
     * Find published lessons that already claim the thing at that time.
     *
     * A timetable repeats every week, so the weekday and the clock decide,
     * not the date.
     *
     * @return array<int, string>
     */
    private function lessonsIn(Facility $facility, Carbon $from, Carbon $to): array
    {
        $records = TimetableRecord::query()
            ->where('facility_id', $facility->id)
            ->with(['timeSlot.timetable.academicCycleSection', 'weekday'])
            ->get();

        if ($records->isEmpty()) {
            return [];
        }

        $clashes = [];

        foreach ($records as $record) {
            $timetable = $record->timeSlot?->timetable;

            if ($timetable === null || !$timetable->isPublished()) {
                continue;
            }

            foreach ($this->daysBetween($from, $to) as $day) {
                if ($this->weekdayNameOf($record) !== strtolower($day->format('l'))) {
                    continue;
                }

                $slot = $record->timeSlot;
                $lessonFrom = $day->copy()->setTimeFromTimeString((string) $slot->start_time);
                $lessonTo = $day->copy()->setTimeFromTimeString((string) $slot->stop_time);

                if ($lessonFrom->lessThan($to) && $lessonTo->greaterThan($from)) {
                    $cycleSection = $timetable->academicCycleSection;
                    $section = $cycleSection === null ? $timetable->name : $cycleSection->name;
                    $clashes[] = sprintf(
                        '%s holds a lesson for %s from %s to %s that day.',
                        $facility->name,
                        $section,
                        $lessonFrom->format('H:i'),
                        $lessonTo->format('H:i'),
                    );
                }
            }
        }

        return $clashes;
    }

    /**
     * Get each day the booking touches.
     *
     * @return array<int, Carbon>
     */
    private function daysBetween(Carbon $from, Carbon $to): array
    {
        $days = [];
        $day = $from->copy()->startOfDay();

        while ($day->lessThanOrEqualTo($to)) {
            $days[] = $day->copy();
            $day->addDay();
        }

        return $days;
    }

    /**
     * Get the weekday a lesson falls on, in lower case.
     */
    private function weekdayNameOf(TimetableRecord $record): string
    {
        $weekday = $record->weekday;

        return $weekday === null ? '' : strtolower((string) $weekday->name);
    }
}
