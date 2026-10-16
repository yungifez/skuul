<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Contracts\View\View;

/**
 * Only published events reach audiences or affect attendance and timetable closures.
 * Livewire handles calendar edits.
 */
class CalendarEventController extends Controller
{
    /**
     * Show one month of the calendar.
     */
    public function index(): View
    {
        $this->authorize('viewAny', CalendarEvent::class);

        return view('pages.calendar-event.index');
    }

    /**
     * Show the form that adds a day to the calendar.
     */
    public function create(): View
    {
        $this->authorize('create', CalendarEvent::class);

        return view('pages.calendar-event.create');
    }

    /**
     * Show one event, and the form that changes it to those who may.
     */
    public function edit(CalendarEvent $calendarEvent): View
    {
        $this->authorize('view', $calendarEvent);

        $calendarEvent->load(['audiences', 'createdBy:id,name']);

        return view('pages.calendar-event.edit', ['event' => $calendarEvent]);
    }
}
