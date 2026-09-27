<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Contracts\View\View;

/**
 * Record a case, follow it, and close it.
 *
 * A safeguarding case is readable only by the people who handle it, so every
 * list here goes through the case's own readable-by rule as well as the policy.
 */
class IncidentController extends Controller
{
    /**
     * Show the cases this person may read.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        return view('pages.incident.index');
    }

    /**
     * Show the form that records a case.
     */
    public function create(): View
    {
        $this->authorize('create', Incident::class);

        return view('pages.incident.create');
    }

    /**
     * Show one case with everything recorded against it.
     */
    public function show(Incident $incident): View
    {
        $this->authorize('view', $incident);

        return view('pages.incident.show', ['incident' => $incident]);
    }
}
