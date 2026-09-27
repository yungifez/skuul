<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Contracts\View\View;

/**
 * The school calendar: what is on, and whether the school is open.
 *
 * Adding, changing, publishing and removing a day happen in the Livewire
 * editor.
 *
 * A draft is not a promise. Only a published event reaches the people it
 * names, and only a published holiday or closure shuts the school for
 * attendance and the timetable.
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
