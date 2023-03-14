<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolSetRequest;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;
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
        $this->schoolService->createSchool($request->validated());

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
    public function settings(): RedirectResponse
    {
        return redirect()->route('schools.edit', ['school' => auth()->user()->school_id]);
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
