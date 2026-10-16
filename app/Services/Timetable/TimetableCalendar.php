<?php

namespace App\Services\Timetable;

use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class TimetableCalendar
{
    public function __construct(private TimetableGrid $grid) {}

    /**
     * Build the timetable grid for the week containing a calendar date.
     *
     * @return array<string, mixed>
     */
    public function gridFor(Timetable $timetable, Carbon $date, ?User $viewer = null): array
    {
        return $this->grid->of($timetable, $date->copy()->startOfWeek(Carbon::MONDAY), $viewer);
    }

    /**
     * Read all entries that occur on one actual date.
     *
     * @param  array<string, mixed>  $grid
     * @param  Collection<int, TimetableTimeSlot>  $slots
     * @param  array<string, int>  $weekdayMap
     * @return array<int, array{key: string, time: string, name: string, kind: string|null, audience_role: string|null}>
     */
    public function eventsForDate(
        Timetable $timetable,
        array $grid,
        Collection $slots,
        array $weekdayMap,
        Carbon $date,
    ): array {
        $weekdayId = $weekdayMap[$date->englishDayOfWeek] ?? null;

        if ($weekdayId === null) {
            return [];
        }

        $events = [];
        $period = $timetable->academicPeriod;

        foreach ($grid['rows'] as $row) {
            $cell = $row['cells'][$weekdayId] ?? null;
            $slot = $slots->get($row['id']);

            if ($cell === null || $slot === null) {
                continue;
            }

            if ($period !== null && $period->starts_on !== null && $period->ends_on !== null && !$period->covers($date)) {
                continue;
            }

            if (!$slot->occursOn($date, $weekdayId)) {
                continue;
            }

            $events[] = [
                'key' => $row['id'].':'.$weekdayId,
                'time' => $row['start'].'–'.$row['stop'],
                'name' => $cell['name'] ?? 'Open time slot',
                'kind' => $cell['kind'],
                'audience_role' => $cell['audience_role'],
            ];
        }

        return $events;
    }
}
