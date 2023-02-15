<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicYearStoreRequest;
use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public $academicYear;

    public function __construct(AcademicYearService $academicYear)
    {
        $this->academicYear = $academicYear;
        $this->authorizeResource(AcademicYear::class, 'academic_year');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.academic-year.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.academic-year.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicYearStoreRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $this->academicYear->createAcademicYear($data);

        return back()->with('success', 'Academic year created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear): View
    {
        return view('pages.academic-year.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear): View
    {
        return view('pages.academic-year.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicYearStoreRequest $request, AcademicYear $academicYear)
    {
        $data = $request->except('_token', '_method');
        $this->academicYear->updateAcademicYear($academicYear, $data);

        return back()->with('success', 'Academic year updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $this->academicYear->deleteAcademicYear($academicYear);

        return back()->with('success', 'Academic year deleted successfully');
    }

    /**
     * Set academic year.
     */
    public function setAcademicYear(request $request): RedirectResponse
    {
        $this->authorize('setAcademicYear', AcademicYear::class);
        $academicYear = $request->academic_year_id;

        $this->academicYear->setAcademicYear($academicYear);

        return back()->with('success', 'Academic year set for '.auth()->user()->school->name.' successfully');
    }
}
