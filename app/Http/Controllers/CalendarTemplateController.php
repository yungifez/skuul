<?php

namespace App\Http\Controllers;

use App\Models\CalendarTemplate;
use App\Models\Organization;
use Illuminate\View\View;

class CalendarTemplateController extends Controller
{
    public function index(Organization $organization): View
    {
        $this->authorize('manageCalendar', $organization);

        $organization->load(['calendarTemplates.periods', 'schools.calendarTemplate']);

        return view('pages.calendar-template.index', compact('organization'));
    }

    public function create(Organization $organization): View
    {
        $this->authorize('manageCalendar', $organization);

        return view('pages.calendar-template.create', compact('organization'));
    }

    public function edit(Organization $organization, CalendarTemplate $calendarTemplate): View
    {
        $this->authorize('manageCalendar', $organization);
        abort_unless($calendarTemplate->organization_id === $organization->id, 404);

        return view('pages.calendar-template.edit', compact('organization', 'calendarTemplate'));
    }
}
