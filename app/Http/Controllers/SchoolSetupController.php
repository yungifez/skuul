<?php

namespace App\Http\Controllers;

use App\Enums\SchoolSetupStep;
use App\Models\School;
use App\Services\School\SchoolSetupProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolSetupController extends Controller
{
    public function __construct(private SchoolSetupProgress $progress) {}

    public function show(School $school, ?string $step = null): View|RedirectResponse
    {
        school_context()->set($school, remember: false);
        $this->authorize('update', $school);
        $progress = $this->progress->for($school);
        $requested = $step === null ? $progress['current'] : SchoolSetupStep::tryFrom($step);

        if (!$requested instanceof SchoolSetupStep) {
            abort(404);
        }

        $current = $progress['current'];
        $requestedComplete = data_get(
            collect($progress['steps'])->firstWhere('value', $requested->value),
            'complete',
            false,
        );

        if ($requested->order() > $current->order() && !$requestedComplete) {
            $requested = $current;
        }

        return view('pages.school.setup', [
            'school' => $school,
            'currentStep' => $requested,
            'progress' => $progress,
        ]);
    }
}
