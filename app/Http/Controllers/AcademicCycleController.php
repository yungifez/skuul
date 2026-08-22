<?php

namespace App\Http\Controllers;

use App\Actions\Calendar\GenerateAcademicCycle;
use App\Http\Requests\GenerateAcademicCycleRequest;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class AcademicCycleController extends Controller
{
    public function __construct(private GenerateAcademicCycle $generateAcademicCycle)
    {
    }

    public function store(GenerateAcademicCycleRequest $request, Organization $organization, CalendarTemplate $calendarTemplate): RedirectResponse
    {
        $this->authorize('manageCalendar', $organization);
        abort_unless($calendarTemplate->organization_id === $organization->id, 404);

        $school = School::findOrFail($request->validated('school_id'));
        $year = $this->generateAcademicCycle->generate(
            $school,
            Carbon::parse($request->validated('starts_on')),
            $calendarTemplate,
            $request->user(),
        );

        return redirect()->route('organizations.calendar-templates.edit', [$organization, $calendarTemplate])
            ->with('success', "Drafted {$year->name} for {$school->name} from {$calendarTemplate->name}.");
    }
}
