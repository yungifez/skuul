<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * The month a calendar screen is showing.
 *
 * The staff calendar and the family calendar step through months the same
 * way, so both read the month and build the grid from here.
 */
trait ReadsCalendarMonths
{
    /**
     * Read the month the screen is showing.
     *
     * A month nobody can read falls back to this one, because a bad link
     * must still open a calendar.
     */
    protected function monthFrom(Request $request): Carbon
    {
        $month = $request->string('month')->toString();

        return rescue(
            fn (): Carbon => Carbon::createFromFormat('Y-m', $month)->startOfMonth(),
            fn (): Carbon => now()->startOfMonth(),
            report: false,
        );
    }

    /**
     * Get every day the month grid shows, whole weeks included.
     *
     * @return array<int, Carbon>
     */
    protected function daysOf(Carbon $month): array
    {
        $day = $month->copy()->startOfMonth()->startOfWeek();
        $last = $month->copy()->endOfMonth()->endOfWeek();
        $days = [];

        while ($day->lte($last)) {
            $days[] = $day->copy();
            $day->addDay();
        }

        return $days;
    }
}
