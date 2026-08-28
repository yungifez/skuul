<?php

namespace App\Http\Controllers;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Http\Requests\ChangeAcademicPeriodStatusRequest;
use App\Http\Requests\SetAcademicPeriodRequest;
use App\Models\AcademicPeriod;
use App\Services\AcademicPeriod\AcademicPeriodService;
use Illuminate\Http\RedirectResponse;

class AcademicPeriodController extends Controller
{
    public function __construct(
        private AcademicPeriodService $academicPeriod,
        private ChangeAcademicPeriodStatus $changeAcademicPeriodStatus,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', AcademicPeriod::class);

        $academicYear = current_academic_year();

        return $academicYear === null
            ? to_route('academic-years.index')
            : to_route('academic-years.show', $academicYear);
    }

    /**
     * Close the academic period and freeze its records.
     */
    public function close(ChangeAcademicPeriodStatusRequest $request, AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->authorize('close', $academicPeriod);

        $this->changeAcademicPeriodStatus->close($academicPeriod, $request->user(), $request->validated('reason'), $request->boolean('force'));

        return back()->with('success', 'Academic period closed successfully');
    }

    /**
     * Reopen the academic period so it accepts work again.
     */
    public function reopen(ChangeAcademicPeriodStatusRequest $request, AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->authorize('reopen', $academicPeriod);

        $this->changeAcademicPeriodStatus->reopen($academicPeriod, $request->user(), $request->validated('reason'));

        return back()->with('success', 'Academic period reopened successfully');
    }

    /**
     * Restrict new work while staff finish the closure checklist.
     */
    public function beginClosing(ChangeAcademicPeriodStatusRequest $request, AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->authorize('close', $academicPeriod);

        $this->changeAcademicPeriodStatus->beginClosing($academicPeriod, $request->user(), $request->validated('reason'));

        return back()->with('success', 'Academic period is now closing. Complete the checklist before final closure.');
    }

    /**
     * Set school academic period.
     */
    public function setAcademicPeriod(SetAcademicPeriodRequest $request): RedirectResponse
    {
        $this->authorize('setAcademicPeriod', AcademicPeriod::class);
        $academicPeriod = AcademicPeriod::inSchool()->findOrFail($request->validated('academic_period_id'));
        $this->academicPeriod->setAcademicPeriod($academicPeriod, $request->user());

        return back()->with('success', 'Working term saved for you.');
    }
}
