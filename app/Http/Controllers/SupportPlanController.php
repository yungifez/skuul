<?php

namespace App\Http\Controllers;

use App\Models\SupportPlan;
use Illuminate\Contracts\View\View;

/**
 * Open a plan of help, run it, and close it.
 *
 * A health or counselling plan is readable only by the people who run it, so
 * every list here goes through the plan's own readable-by rule as well as the
 * policy.
 */
class SupportPlanController extends Controller
{
    /**
     * Show the plans this person may read.
     */
    public function index(): View
    {
        $this->authorize('viewAny', SupportPlan::class);

        return view('pages.support-plan.index');
    }

    /**
     * Show the form that opens a plan.
     */
    public function create(): View
    {
        $this->authorize('create', SupportPlan::class);

        return view('pages.support-plan.create');
    }

    /**
     * Show one plan with everything recorded against it.
     */
    public function show(SupportPlan $supportPlan): View
    {
        $this->authorize('view', $supportPlan);

        return view('pages.support-plan.show', ['plan' => $supportPlan]);
    }
}
