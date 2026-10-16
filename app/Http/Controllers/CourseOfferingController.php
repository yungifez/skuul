<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\CourseOffering;
use Illuminate\View\View;

class CourseOfferingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CourseOffering::class, 'courseOffering');
    }

    public function index(): View
    {
        return view('pages.course-offering.index');
    }

    public function create(): View
    {
        return view('pages.course-offering.create');
    }

    public function bulkCreate(): View
    {
        $this->authorize('viewAny', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()->with('topLevelPeriods')->orderByDesc('start_year')->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', request()->integer('academic_year_id'))
            ?? $academicYears->firstWhere('id', current_academic_year_id())
            ?? $academicYears->first();

        abort_unless($selectedAcademicYear instanceof AcademicYear, 404);

        return view('pages.course-offering.bulk-create', compact('selectedAcademicYear'));
    }

    public function bulkCreateForm(): View
    {
        $this->authorize('create', CourseOffering::class);

        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->get();
        $selectedAcademicYear = $academicYears->firstWhere('id', request()->integer('academic_year_id'))
            ?? $academicYears->firstWhere('id', current_academic_year_id())
            ?? $academicYears->first();

        abort_unless($selectedAcademicYear instanceof AcademicYear, 404);

        return view('pages.course-offering.bulk-create-form', compact('selectedAcademicYear'));
    }

    public function rollForwardForm(): View
    {
        $this->authorize('create', CourseOffering::class);

        return view('pages.course-offering.roll-forward');
    }

    public function edit(CourseOffering $courseOffering): View
    {
        return view('pages.course-offering.edit', compact('courseOffering'));
    }
}
