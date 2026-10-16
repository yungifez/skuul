<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct(
        private AcademicYearService $academicYear,
    ) {
        $this->authorizeResource(AcademicYear::class, 'academic_year');
    }

    public function index(): View
    {
        return view('pages.academic-year.index');
    }

    public function create(): View
    {
        return view('pages.academic-year.create');
    }

    public function show(AcademicYear $academicYear): View
    {
        return view('pages.academic-year.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('pages.academic-year.edit', compact('academicYear'));
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $this->academicYear->deleteAcademicYear($academicYear);

        return back()->with('success', 'School calendar deleted successfully');
    }
}
