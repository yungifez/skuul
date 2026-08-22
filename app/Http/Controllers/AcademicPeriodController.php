<?php

namespace App\Http\Controllers;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Http\Requests\AcademicPeriodStoreRequest;
use App\Http\Requests\ChangeAcademicPeriodStatusRequest;
use App\Http\Requests\SetAcademicPeriodRequest;
use App\Models\AcademicPeriod;
use App\Services\AcademicPeriod\AcademicPeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AcademicPeriodController extends Controller
{
    public $academicPeriod;

    public function __construct(AcademicPeriodService $academicPeriod, private ChangeAcademicPeriodStatus $changeAcademicPeriodStatus)
    {
        $this->academicPeriod = $academicPeriod;
        $this->authorizeResource(AcademicPeriod::class, 'academicPeriod');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.academic-period.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return to_route('academic-periods.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicPeriodStoreRequest $request): RedirectResponse
    {
        $this->academicPeriod->createAcademicPeriod($request->validated());

        return back()->with('success', 'Successfully created academic period');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicPeriod $academicPeriod): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicPeriod $academicPeriod): View
    {
        return view('pages.academic-period.edit', compact('academicPeriod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AcademicPeriodStoreRequest $request, AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->academicPeriod->updateAcademicPeriod($academicPeriod, $request->validated());

        return back()->with('success', 'Successfully updated academic period');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->academicPeriod->deleteAcademicPeriod($academicPeriod);

        return back()->with('success', 'Successfully deleted academic period');
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
        $this->academicPeriod->setAcademicPeriod($academicPeriod);

        return back()->with('success', 'Successfully set current academic period');
    }
}
