<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\School;
use App\Services\School\SchoolService;
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
    public function __construct(SchoolService $schoolService)
    {
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
        $this->schoolService->createSchool($attributes);

        return back()->with('success', __('School created successfully'));
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
        return view('pages.school.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SchoolUpdateRequest $request, School $school): RedirectResponse
    {
        $this->schoolService->updateSchool($school, $request->validated());

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
        $academicYear = current_academic_year();

        $this->authorize('update', $school);

        $academicLevelsCount = AcademicLevel::query()->inSchool($school)->count();
        $cycleSectionsCount = $academicYear === null
            ? 0
            : AcademicCycleSection::query()
                ->inSchool($school)
                ->where('academic_year_id', $academicYear->id)
                ->count();
        $courseOfferingsCount = $academicYear === null
            ? 0
            : CourseOffering::query()
                ->inSchool($school)
                ->where('academic_year_id', $academicYear->id)
                ->count();

        $needsAttention = [];

        if ($academicYear === null) {
            $needsAttention[] = [
                'title' => 'School calendar',
                'reason' => 'No current school year is selected. Create or choose one before setting up this year.',
            ];
        }

        if ($academicLevelsCount === 0) {
            $needsAttention[] = [
                'title' => 'Grades and classes',
                'reason' => 'No grade or class levels have been added yet.',
            ];
        }

        if ($cycleSectionsCount === 0) {
            $needsAttention[] = [
                'title' => 'Classes this year',
                'reason' => $academicYear === null
                    ? 'Choose a current school year first, then create its arms, homerooms or sections.'
                    : 'No classes have been created for the current school year.',
            ];
        }

        if ($courseOfferingsCount === 0) {
            $needsAttention[] = [
                'title' => 'Subjects being taught',
                'reason' => $academicYear === null
                    ? 'Choose a current school year first, then set up the subjects being taught.'
                    : 'No subjects have been set up for the current school year.',
            ];
        }

        return view('pages.school.settings', [
            'school' => $school,
            'academicYear' => $academicYear,
            'academicLevelsCount' => $academicLevelsCount,
            'cycleSectionsCount' => $cycleSectionsCount,
            'courseOfferingsCount' => $courseOfferingsCount,
            'needsAttention' => $needsAttention,
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
