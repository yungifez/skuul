<?php

namespace App\Http\Controllers;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Enums\AcademicYearSetupStep;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Subject;
use App\Services\AcademicYear\AcademicYearService;
use App\Services\AcademicYear\AcademicYearSetupProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearSetupController extends Controller
{
    public function __construct(
        private AcademicYearSetupProgress $progress,
        private PublishAcademicCalendar $publishAcademicCalendar,
        private AcademicYearService $academicYears,
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

        $academicYear = $academicYear->load('topLevelPeriods');
        $academicLevels = collect();
        $subjects = collect();
        $courseOfferings = collect();
        $previousAcademicYear = null;

        if ($requested === AcademicYearSetupStep::Structure) {
            $academicYear->load(['cycleSections.academicLevel', 'cycleSections.homeroomTeacher']);
            $academicLevels = AcademicLevel::inSchool($academicYear->school_id)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'parent_id', 'name', 'status', 'is_group']);
        }

        if ($requested === AcademicYearSetupStep::Subjects) {
            $subjects = Subject::inSchool($academicYear->school_id)
                ->orderBy('name')
                ->get(['id', 'name', 'short_name']);
            $courseOfferings = CourseOffering::inSchool($academicYear->school_id)
                ->where('academic_year_id', $academicYear->id)
                ->with([
                    'subject:id,name,short_name',
                    'academicLevel:id,name',
                    'academicPeriod:id,name,label',
                    'cycleSections:id,name,label',
                ])
                ->orderBy('subject_id')
                ->orderBy('academic_level_id')
                ->orderBy('academic_period_id')
                ->get();
            $previousAcademicYear = AcademicYear::inSchool($academicYear->school_id)
                ->where('start_year', '<', $academicYear->start_year)
                ->orderByDesc('start_year')
                ->orderByDesc('id')
                ->first();
        }

        return view('pages.academic-year.setup', [
            'academicYear' => $academicYear,
            'currentStep' => $requested,
            'progress' => $progress,
            'academicLevels' => $academicLevels,
            'previousAcademicYear' => $previousAcademicYear,
            'subjects' => $subjects,
            'courseOfferings' => $courseOfferings,
        ]);
    }

    public function publish(AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('update', $academicYear);

        try {
            $academicYear = $this->publishAcademicCalendar->publish($academicYear, request()->user());
            $this->academicYears->setSchoolDefaultAcademicYear($academicYear);
        } catch (InvalidValueException $exception) {
            return to_route('academic-years.setup', [$academicYear, AcademicYearSetupStep::Review->value])
                ->withErrors(['setup' => $exception->getMessage()]);
        }

        return to_route('academic-years.show', $academicYear)
            ->with('success', 'The academic year is ready. Its school calendar is now available.');
    }
}
