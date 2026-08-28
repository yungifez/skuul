<?php

namespace App\Http\Controllers;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Http\Requests\ChangeAcademicPeriodStatusRequest;
use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct(
        private AcademicYearService $academicYear,
        private ChangeAcademicPeriodStatus $changeAcademicPeriodStatus,
    ) {
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
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $this->academicYear->deleteAcademicYear($academicYear);

        return back()->with('success', 'School calendar deleted successfully');
    }

    /**
     * Close the academic year and freeze its records.
     */
    public function close(ChangeAcademicPeriodStatusRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('close', $academicYear);

        $this->changeAcademicPeriodStatus->close($academicYear, $request->user(), $request->validated('reason'), $request->boolean('force'));

        return back()->with('success', 'Academic year closed successfully');
    }

    /**
     * Reopen the academic year so it accepts work again.
     */
    public function reopen(ChangeAcademicPeriodStatusRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('reopen', $academicYear);

        $this->changeAcademicPeriodStatus->reopen($academicYear, $request->user(), $request->validated('reason'));

        return back()->with('success', 'Academic year reopened successfully');
    }

    /**
     * Restrict new work while staff finish the cycle closure checklist.
     */
    public function beginClosing(ChangeAcademicPeriodStatusRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('close', $academicYear);

        $this->changeAcademicPeriodStatus->beginClosing($academicYear, $request->user(), $request->validated('reason'));

        return back()->with('success', 'Academic cycle is now closing. Complete the checklist before final closure.');
    }

    /**
     * Set academic year.
     */
    public function setAcademicYear(Request $request): RedirectResponse
    {
        $this->authorize('setAcademicYear', AcademicYear::class);
        $academicYear = $request->academic_year_id;

        $this->academicYear->setAcademicYear((int) $academicYear, $request->user());

        return back()->with('success', 'Working calendar set for '.current_school()->name.' successfully');
    }
}
