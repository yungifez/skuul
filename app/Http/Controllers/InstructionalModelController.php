<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\GrantOfferingException;
use App\Actions\Curriculum\MigrateInstructionalModel;
use App\Actions\Curriculum\SetInstructionalModel;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Http\Requests\GrantOfferingExceptionRequest;
use App\Http\Requests\MigrateInstructionalModelRequest;
use App\Http\Requests\SetInstructionalModelRequest;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelException;
use App\Models\InstructionalModelMigration;
use App\Models\Subject;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Services\Curriculum\OfferingExceptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The way a campus teaches an academic cycle.
 *
 * The choice sits with the cycle it belongs to, so staff answer it while they
 * set the cycle up rather than on a settings page of its own.
 */
class InstructionalModelController extends Controller
{
    public function __construct(
        private SetInstructionalModel $setInstructionalModel,
        private MigrateInstructionalModel $migrateInstructionalModel,
        private InstructionalModelResolver $resolver,
        private OfferingExceptions $exceptions,
        private GrantOfferingException $grantException,
    ) {}

    /**
     * Show the one question that sets up teaching for the cycle.
     */
    public function edit(AcademicYear $academicYear): View
    {
        $this->authorize('viewInstructionalModel', $academicYear);

        $setting = $this->resolver->settingFor($academicYear);

        $model = $setting === null ? InstructionalModel::default() : $setting->model;
        $user = request()->user();

        return view('pages.instructional-model.edit', [
            'academicYear' => $academicYear,
            'setting' => $setting?->loadMissing('updatedBy'),
            'model' => $model,
            'isFutureCycle' => $this->setInstructionalModel->isFutureCycle($academicYear),
            'canSet' => $academicYear->exists && $user?->can('setInstructionalModel', $academicYear) === true,
            'canMigrate' => $user?->can('migrateInstructionalModel', $academicYear) === true
                && $this->migrateInstructionalModel->canBeMigrated($academicYear),
            'impacts' => $this->impactsFor($academicYear, $model),
            'migrations' => InstructionalModelMigration::where('school_id', $academicYear->school_id)
                ->where('academic_year_id', $academicYear->id)
                ->with('migratedBy')
                ->latest('id')
                ->get(),
            'exceptions' => $this->exceptions->forCycle($academicYear),
            'canExcept' => $user?->can('setInstructionalModel', $academicYear) === true,
            'subjects' => Subject::inSchool($academicYear->school_id)->orderBy('name')->get(),
            'academicLevels' => AcademicLevel::inSchool($academicYear->school_id)->orderBy('position')->orderBy('name')->get(),
            'exceptionModes' => array_values(array_filter(
                RosterMode::cases(),
                fn (RosterMode $mode): bool => !$model->allowsRosterMode($mode),
            )),
        ]);
    }

    /**
     * Let one subject be taught outside the campus model.
     */
    public function grantException(GrantOfferingExceptionRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('setInstructionalModel', $academicYear);

        $this->grantException->grant(
            academicYear: $academicYear,
            subject: Subject::inSchool($academicYear->school_id)->findOrFail($request->validated('subject_id')),
            rosterMode: RosterMode::from($request->validated('roster_mode')),
            reason: $request->validated('reason'),
            academicLevel: $request->validated('academic_level_id') === null
                ? null
                : AcademicLevel::inSchool($academicYear->school_id)->findOrFail($request->validated('academic_level_id')),
        );

        return redirect()
            ->route('academic-years.instructional-model.edit', $academicYear->id)
            ->with('success', 'The exception was recorded. The campus model has not moved.');
    }

    /**
     * Take an exception back.
     */
    public function revokeException(AcademicYear $academicYear, InstructionalModelException $exception): RedirectResponse
    {
        $this->authorize('setInstructionalModel', $academicYear);

        abort_unless($exception->academic_year_id === $academicYear->id, 404);

        $this->grantException->revoke($exception);

        return redirect()
            ->route('academic-years.instructional-model.edit', $academicYear->id)
            ->with('success', 'The exception was taken back. Classes already running are left alone.');
    }

    /**
     * Move a running cycle to another model, and record why.
     */
    public function migrate(MigrateInstructionalModelRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('migrateInstructionalModel', $academicYear);

        $model = InstructionalModel::from($request->validated('model'));

        $this->migrateInstructionalModel->migrate(
            $academicYear,
            $model,
            (string) $request->validated('reason'),
            $request->user(),
        );

        return redirect()
            ->route('academic-years.instructional-model.edit', $academicYear)
            ->with('success', "{$academicYear->name} now teaches with: {$model->label()}");
    }

    /**
     * Work out what a move to each other model would meet in this cycle.
     *
     * @return array<string, array<string, mixed>>
     */
    private function impactsFor(AcademicYear $academicYear, InstructionalModel $model): array
    {
        $impacts = [];

        foreach (InstructionalModel::cases() as $option) {
            if ($option === $model) {
                continue;
            }

            $impacts[$option->value] = $this->migrateInstructionalModel->impactOf($academicYear, $option);
        }

        return $impacts;
    }

    /**
     * Record the model the campus teaches the cycle with.
     */
    public function update(SetInstructionalModelRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->authorize('setInstructionalModel', $academicYear);

        $model = InstructionalModel::from($request->validated('model'));

        $this->setInstructionalModel->set($academicYear, $model, $request->user(), $request->validated('reason'));

        if (request()->boolean('setup')) {
            return to_route('academic-years.setup', [$academicYear, 'structure'])
                ->with('success', 'Teaching approach saved. Now build this year’s classes.');
        }

        return back()->with('success', "{$academicYear->name} now teaches with: {$model->label()}");
    }
}
