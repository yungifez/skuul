<?php

namespace App\Http\Controllers;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\CalendarEvent;
use App\Models\StudentRecord;
use App\Services\Calendar\SchoolCalendar;
use App\Services\Portal\PortalAccess;
use App\Traits\ReadsCalendarMonths;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The calendar one family reads.
 *
 * The family sees only what the school published, and only the days that
 * reach their child: a day for the whole school, a day for their home group,
 * or a day that names them. A draft never appears here.
 */
class PortalCalendarController extends Controller
{
    use ReadsCalendarMonths;

    public function __construct(private PortalAccess $access, private SchoolCalendar $calendar) {}

    /**
     * Show one month of the calendar for one enrollment.
     */
    public function index(Request $request, StudentRecord $studentRecord): View
    {
        abort_unless($this->access->canRead($request->user(), $studentRecord), 403);
        abort_unless($this->access->areaIsOpen(PortalArea::Calendar, $studentRecord->school_id), 404);
        // A family holds no working school, so the feature is read against the
        // school the child attends.
        abort_unless(features()->enabled(Feature::Events, $studentRecord->school_id), 404);

        $month = $this->monthFrom($request);
        $events = $this->calendar->forEnrollment(
            $studentRecord,
            $month->copy()->startOfMonth(),
            $month->copy()->endOfMonth(),
        );

        return view('pages.portal.calendar', [
            'studentRecord' => $studentRecord,
            'month' => $month,
            'days' => $this->daysOf($month),
            'events' => $events,
            // The days off this family reads, not the school's whole list.
            'closures' => $events->reject(fn (CalendarEvent $event): bool => $event->isTeachingDay()),
        ]);
    }
}
