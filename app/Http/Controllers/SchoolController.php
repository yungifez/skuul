<?php

namespace App\Http\Controllers;

use App\Actions\School\GrantSchoolMembership;
use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Services\School\SchoolService;
use App\Services\School\SchoolSetupChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolController extends Controller
{
    /**
     * @var SchoolService
     */
    public $schoolService;

    /**
     * SchoolController constructor.
     */
    public function __construct(
        SchoolService $schoolService,
        private SchoolSetupChecklist $schoolSetupChecklist,
        private GrantSchoolMembership $grantSchoolMembership,
    ) {
        $this->schoolService = $schoolService;
        $this->authorizeResource(School::class, 'school');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.school.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.school.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SchoolStoreRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $organization = Organization::findOrFail($attributes['organization_id']);

        $this->authorize('createForOrganization', [School::class, $organization]);
        $school = $this->schoolService->createSchool($attributes);
        $school->operatingProfile()->firstOrCreate([], [
            'preset' => 'home_sections',
            'labels' => SchoolOperatingProfile::labelsFor('home_sections'),
        ]);
        $this->grantSchoolMembership->grant($request->user(), $school, primary: false);
        school_context()->set($school, remember: false);

        return to_route('schools.setup', [$school, 'details'])
            ->with('success', __('School created successfully. Let’s set up the essentials.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school): View
    {
        return view('pages.school.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school): View
    {
        return view('pages.school.edit', [
            'school' => $school,
            'setup' => request()->boolean('setup'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SchoolUpdateRequest $request, School $school): RedirectResponse
    {
        $this->schoolService->updateSchool($school, $request->validated());

        if ($request->boolean('setup')) {
            return to_route('schools.setup', [$school, 'language'])
                ->with('success', __('School details updated.'));
        }

        return back()->with('success', __('School updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school): RedirectResponse
    {
        $this->schoolService->deleteSchool($school);

        return back()->with('success', __('School deleted successfully'));
    }

    /**
     * Get settings for authenticated user's school.
     */
    public function settings(): View
    {
        $school = current_school();

        $this->authorize('update', $school);

        $setupChecklist = $this->schoolSetupChecklist->for($school);

        return view('pages.school.settings', [
            'school' => $school,
            'academicYear' => $setupChecklist['academicYear'],
            'academicLevelsCount' => $setupChecklist['counts']['academicLevels'],
            'cycleSectionsCount' => $setupChecklist['counts']['cycleSections'],
            'courseOfferingsCount' => $setupChecklist['counts']['courseOfferings'],
            'setupChecklist' => $setupChecklist,
        ]);
    }

    /**
     * Set school.
     */
    public function setSchool(SchoolSetRequest $request): RedirectResponse
    {
        $this->authorize('setSchool', School::class);

        $schoolId = $request->input('school_id');
        $school = School::findOrFail($schoolId);

        $this->schoolService->setSchool($school);

        return back()->with('success', __('School set successfully'));
    }
}
