<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use Illuminate\Contracts\View\View;

/**
 * A named group of people that is not a class and not a section.
 *
 * The group screens are Livewire components. A place in a group is kept, not
 * deleted, so a school can still see who was in a group last year.
 */
class CohortController extends Controller
{
    /**
     * Show the groups this person may read.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Cohort::class);

        return view('pages.cohort.index');
    }

    /**
     * Show the form that makes a group.
     */
    public function create(): View
    {
        $this->authorize('create', Cohort::class);

        return view('pages.cohort.create');
    }

    /**
     * Show one group and who is in it.
     */
    public function show(Cohort $cohort): View
    {
        $this->authorize('view', $cohort);

        return view('pages.cohort.show', ['cohort' => $cohort]);
    }
}
