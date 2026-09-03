<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Put a group of learners in order by their published results.
 *
 * A position is worked out when it is asked for, never stored. Correcting a
 * result changes the order the next time somebody opens this screen, and
 * nothing has to be rewritten.
 */
class RankingController extends Controller
{
    /**
     * Show the rankings screen. The Livewire component owns its filter state
     * so selections can update the sections, subjects, and results in place.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('read ranking'), 403);

        return view('pages.ranking.index');
    }
}
