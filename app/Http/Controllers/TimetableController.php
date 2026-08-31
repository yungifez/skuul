<?php

namespace App\Http\Controllers;

use App\Actions\Timetable\CreateSectionTimetableOverride;
use App\Actions\Timetable\CreateTimetableSubstitution;
use App\Actions\Timetable\PublishTimetable;
use App\Actions\Timetable\ReviseTimetable;
use App\Enums\Role;
use App\Http\Requests\CreateSectionTimetableOverrideRequest;
use App\Http\Requests\StoreTimetableSubstitutionRequest;
use App\Http\Requests\TimetableStoreRequest;
use App\Http\Requests\TimetableUpdateRequest;
use App\Models\AcademicCycleSection;
use App\Models\Timetable;
use App\Models\TimetableRecord;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Services\Timetable\TimetableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TimetableController extends Controller
{
    public $timetableService;

    public function __construct(
        TimetableService $timetableService,
        private PublishTimetable $publishTimetable,
        private ReviseTimetable $reviseTimetable,
        private CreateSectionTimetableOverride $createSectionTimetableOverride,
        private CreateTimetableSubstitution $createTimetableSubstitution,
    ) {
        $this->timetableService = $timetableService;
        $this->authorizeResource(Timetable::class, 'timetable');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.timetable.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.timetable.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimetableStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $data['academic_period_id'] = current_academic_period_id();

        $timetable = $this->timetableService->createTimetable($data);

        return to_route('timetables.manage', $timetable->id)->with('success', 'Timetable created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Timetable $timetable): View
    {
        $overrideSections = $timetable->academicCycleSection === null
            ? collect()
            : AcademicCycleSection::inSchool()->where('academic_year_id', $timetable->academicCycleSection->academic_year_id)->where('academic_level_id', $timetable->academicCycleSection->academic_level_id)->whereKeyNot($timetable->academic_cycle_section_id)->orderBy('position')->get(['id', 'name', 'label']);
        $substitutionEntries = TimetableRecord::query()
            ->join('timetable_time_slots', 'timetable_time_slot_weekday.timetable_time_slot_id', '=', 'timetable_time_slots.id')
            ->join('weekdays', 'timetable_time_slot_weekday.weekday_id', '=', 'weekdays.id')
            ->where('timetable_time_slots.timetable_id', $timetable->id)
            ->orderBy('timetable_time_slot_weekday.weekday_id')
            ->orderBy('timetable_time_slots.start_time')
            ->get([
                'timetable_time_slot_weekday.timetable_time_slot_id',
                'timetable_time_slot_weekday.weekday_id',
                'timetable_time_slots.start_time',
                'timetable_time_slots.stop_time',
                'weekdays.name as weekday_name',
            ]);
        $replacementTeachers = User::ofSchool()
            ->role(Role::Teacher->value)
            ->get(['users.id', 'users.name']);
        $substitutions = $timetable->substitutions()
            ->with(['timeSlot:id,start_time,stop_time', 'weekday:id,name', 'replacementTeacher:id,name', 'approvedBy:id,name'])
            ->latest('substituted_on')
            ->get();

        return view('pages.timetable.show', compact('timetable', 'overrideSections', 'substitutionEntries', 'replacementTeachers', 'substitutions'));
    }

    /**
     * Print timetable.
     */
    public function print(Timetable $timetable): Response
    {
        $data['timetable'] = $timetable;

        return $this->timetableService->printTimetable($data['timetable']->name, 'pages.timetable.print', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timetable $timetable): View
    {
        return view('pages.timetable.edit', compact('timetable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimetableUpdateRequest $request, Timetable $timetable): RedirectResponse
    {
        $data = $request->except('_token'.'_method');
        $this->timetableService->updateTimetable($timetable, $data);

        return back()->with('success', 'Timetable updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timetable $timetable): RedirectResponse
    {
        $this->timetableService->deleteTimetable($timetable);

        return back()->with('success', 'Timetable deleted successfully');
    }

    /**
     * Manage Timetable.
     */
    public function manage(Timetable $timetable): View
    {
        $this->authorize('update', $timetable);

        return view('pages.timetable.manage', compact('timetable'));
    }

    /**
     * Publish the draft timetable after its conflicts have been checked.
     */
    public function publish(Request $request, Timetable $timetable): RedirectResponse
    {
        $this->authorize('publish', $timetable);
        $this->publishTimetable->publish($timetable, $request->user());

        return back()->with('success', 'Timetable published successfully');
    }

    /**
     * Start a new editable revision from a published timetable.
     */
    public function revise(Request $request, Timetable $timetable): RedirectResponse
    {
        $this->authorize('revise', $timetable);
        $draft = $this->reviseTimetable->revise($timetable, $request->user());

        return to_route('timetables.manage', $draft)->with('success', 'New timetable revision created');
    }

    public function createSectionOverride(CreateSectionTimetableOverrideRequest $request, Timetable $timetable): RedirectResponse
    {
        $section = AcademicCycleSection::inSchool()->findOrFail($request->integer('academic_cycle_section_id'));
        $override = $this->createSectionTimetableOverride->create($timetable, $section, $request->user());

        return to_route('timetables.manage', $override)->with('success', 'Section timetable draft created from the published template.');
    }

    public function storeSubstitution(StoreTimetableSubstitutionRequest $request, Timetable $timetable): RedirectResponse
    {
        $data = $request->validated();
        [$timeSlotId, $weekdayId] = array_map('intval', explode(':', $data['timetable_entry'], 2));
        $slot = TimetableTimeSlot::query()->findOrFail($timeSlotId);
        $teacher = User::ofSchool()->findOrFail($data['replacement_teacher_id']);
        $actor = $request->user();

        abort_unless($actor instanceof User, 403);

        $this->createTimetableSubstitution->create($timetable, $slot, $weekdayId, $teacher, now()->parse($data['substituted_on']), $data['reason'], $actor);

        return back()->with('success', 'Substitution recorded without changing the published timetable.');
    }
}
