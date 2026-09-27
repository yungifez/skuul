<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicPeriodRequest;
use App\Http\Requests\UpdateAcademicPeriodRequest;
use App\Models\AcademicPeriod;
use App\Services\AcademicPeriod\AcademicPeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicPeriodController extends Controller
{
    public function __construct(
        private AcademicPeriodService $academicPeriod,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', AcademicPeriod::class);

        $academicYear = current_academic_year();

        return $academicYear === null
            ? to_route('academic-years.index')->with('info', 'Choose a working school year before opening reporting periods.')
            : to_route('academic-years.show', $academicYear);
    }

    /**
     * Show the academic period editor.
     */
    public function edit(AcademicPeriod $academicPeriod): View
    {
        $this->authorize('update', $academicPeriod);

        return view('pages.academic-period.edit', compact('academicPeriod'));
    }

    /**
     * Show the academic period creation form.
     */
    public function create(): View
    {
        $this->authorize('create', AcademicPeriod::class);

        return view('pages.academic-period.create');
    }

    /**
     * Create an academic period in the working academic year.
     */
    public function store(StoreAcademicPeriodRequest $request): RedirectResponse
    {
        $academicPeriod = $this->academicPeriod->createAcademicPeriod($request->validated());

        return to_route('academic-years.show', $academicPeriod->academic_year_id)
            ->with('success', 'Academic period created.');
    }

    /**
     * Update the academic period dates and label.
     */
    public function update(UpdateAcademicPeriodRequest $request, AcademicPeriod $academicPeriod): RedirectResponse
    {
        $this->academicPeriod->updateAcademicPeriod($academicPeriod, $request->validated());

        return to_route('academic-years.show', $academicPeriod->academic_year_id)
            ->with('success', 'Academic period updated.');
    }
}
