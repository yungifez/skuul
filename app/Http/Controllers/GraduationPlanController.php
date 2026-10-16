<?php

namespace App\Http\Controllers;

use App\Models\GraduationPlan;
use Illuminate\Contracts\View\View;

/**
 * What a learner must finish before the school will let them graduate.
 *
 * The plan screens are Livewire components. Only a published result counts
 * towards a plan, so a plan never reads a mark a family has not seen.
 */
class GraduationPlanController extends Controller
{
    /**
     * Show the plans this school keeps. A stage is reached through its plan.
     */
    public function index(): View
    {
        $this->authorize('viewAny', GraduationPlan::class);

        return view('pages.graduation-plan.index', [
            'plans' => GraduationPlan::query()
                ->inSchool()
                ->whereNull('parent_id')
                ->with('cohort:id,name')
                ->withCount(['requirements', 'children'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    /**
     * Show the form that writes a plan.
     */
    public function create(): View
    {
        $this->authorize('create', GraduationPlan::class);

        return view('pages.graduation-plan.create');
    }

    /**
     * Show one plan or stage.
     */
    public function show(GraduationPlan $graduationPlan): View
    {
        $this->authorize('view', $graduationPlan);

        return view('pages.graduation-plan.show', ['plan' => $graduationPlan]);
    }
}
