<?php

namespace App\Http\Controllers;

use App\Actions\Calendar\SetCampusCalendarTemplate;
use App\Http\Requests\SetCampusCalendarTemplateRequest;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CalendarTemplateController extends Controller
{
    public function __construct(
        private SetCampusCalendarTemplate $setCampusCalendarTemplate,
    ) {}

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

        $calendarTemplate->load('periods');
        $organization->load('schools.calendarTemplate');

        return view('pages.calendar-template.edit', compact('organization', 'calendarTemplate'));
    }

    public function overrideCampus(SetCampusCalendarTemplateRequest $request, Organization $organization, CalendarTemplate $calendarTemplate, School $school): RedirectResponse
    {
        $this->authorize('manageCalendar', $organization);
        abort_unless($calendarTemplate->organization_id === $organization->id && $school->organization_id === $organization->id, 404);

        $this->setCampusCalendarTemplate->override($school, $calendarTemplate, $request->user(), $request->validated('reason'));

        return back()->with('success', "{$school->name} now follows {$calendarTemplate->name}.");
    }

    public function inheritCampus(SetCampusCalendarTemplateRequest $request, Organization $organization, CalendarTemplate $calendarTemplate, School $school): RedirectResponse
    {
        $this->authorize('manageCalendar', $organization);
        abort_unless($calendarTemplate->organization_id === $organization->id && $school->organization_id === $organization->id, 404);

        $this->setCampusCalendarTemplate->inherit($school, $request->user(), $request->validated('reason'));

        return back()->with('success', "{$school->name} now follows the organization default calendar.");
    }
}
