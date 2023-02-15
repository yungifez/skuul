<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\School\SchoolService;
use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;

class SchoolController extends Controller
{
    /**
     * @var SchoolService
     */
    public $schoolService;

    /**
     * SchoolController constructor.
     *
     * @param SchoolService $schoolService
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
     *
     * @param SchoolStoreRequest $request
     */
    public function store(SchoolStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->schoolService->createSchool($data);

        return back()->with('success', __('School created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param School $school
     */
    public function show(School $school): View
    {
        $data['school'] = $school;

        return view('pages.school.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param School $school
     */
    public function edit(School $school): View
    {
        $data['school'] = $school;

        return view('pages.school.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SchoolUpdateRequest $request
     * @param School              $school
     */
    public function update(SchoolUpdateRequest $request, School $school): RedirectResponse
    {
        $data = $request->except('_token', '_method');
        $this->schoolService->updateSchool($school, $data);

        return back()->with('success', __('School updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param School $school
     */
    public function destroy(School $school): RedirectResponse
    {
        $this->schoolService->deleteSchool($school);

        return back()->with('success', __('School deleted successfully'));
    }

    /**
     * Get settings for authenticated user's school.
     */
    public function settings(): RedirectResponse
    {
        return redirect()->route('schools.edit', ['school' => auth()->user()->school_id]);
    }

    /**
     * Set the school.
     *
     * @param SchoolSetRequest $request
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
