<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\School\SchoolSetupPhaseService;
use Illuminate\Http\RedirectResponse;

class SchoolSetupPhaseController extends Controller
{
    public function __construct(private SchoolSetupPhaseService $phases) {}

    /**
     * Acknowledge that the current school setup is ready for daily work.
     */
    public function acknowledge(): RedirectResponse
    {
        $school = current_school();
        $this->authorize('update', $school);

        /** @var User $actor */
        $actor = auth()->user();
        $this->phases->acknowledge($school, $actor);

        return to_route('dashboard')->with('success', 'Your school is ready for daily work.');
    }
}
