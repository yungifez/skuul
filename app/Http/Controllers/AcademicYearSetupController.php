<?php

namespace App\Http\Controllers;

use App\Enums\AcademicYearSetupStep;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Services\AcademicYear\AcademicYearSetupProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearSetupController extends Controller
{
    public function __construct(
        private AcademicYearSetupProgress $progress,
    ) {}

    public function show(AcademicYear $academicYear, ?string $step = null): View|RedirectResponse
    {
        $this->authorize('update', $academicYear);
        $progress = $this->progress->for($academicYear);
        $requested = $step === null ? $progress['current'] : AcademicYearSetupStep::tryFrom($step);

        if (!$requested instanceof AcademicYearSetupStep) {
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

        if ($requested === AcademicYearSetupStep::Subjects) {
            return to_route('course-offerings.bulk-create', [
                'academic_year_id' => $academicYear->id,
                'setup' => 1,
            ]);
        }

        $academicYear = $academicYear->load('topLevelPeriods');
        $academicLevels = collect();

        if ($requested === AcademicYearSetupStep::Structure) {
            $academicYear->load([
                'cycleSections.academicLevel',
                'cycleSections.academicYear',
                'cycleSections.homeroomTeacher',
            ]);
            $academicLevels = AcademicLevel::inSchool($academicYear->school_id)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'name', 'status', 'is_group']);
        }

        return view('pages.academic-year.setup', [
            'academicYear' => $academicYear,
            'currentStep' => $requested,
            'progress' => $progress,
            'academicLevels' => $academicLevels,
        ]);
    }
}
