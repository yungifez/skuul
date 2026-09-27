<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * What a campus plans to spend and take in, beside what it actually did.
 *
 * Writing, revising and removing plans happens in the Livewire planner.
 */
class BudgetController extends Controller
{
    /**
     * Show the plans of one cycle and how they are running.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Budget::class);

        return view('pages.fee.budget.index');
    }
}
