<?php

namespace App\Http\Controllers;

use App\Models\CampusMoveRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampusMoveRequestController extends Controller
{
    /**
     * Show the campus moves this campus must decide, and the ones it asked for.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', CampusMoveRequest::class);

        return view('pages.campus-move.index');
    }
}
