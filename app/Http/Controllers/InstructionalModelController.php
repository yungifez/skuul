<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\MigrateInstructionalModel;
use App\Actions\Curriculum\SetInstructionalModel;
use App\Enums\InstructionalModel;
use App\Http\Requests\MigrateInstructionalModelRequest;
use App\Http\Requests\SetInstructionalModelRequest;
use App\Models\AcademicYear;
use App\Models\InstructionalModelMigration;
use App\Services\Curriculum\InstructionalModelResolver;
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
    ) {
    }

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
            'academicYear'  => $academicYear,
            'setting'       => $setting?->loadMissing('updatedBy'),
            'model'         => $model,
            'isFutureCycle' => $this->setInstructionalModel->isFutureCycle($academicYear),
            'canSet'        => $academicYear->exists && $user?->can('setInstructionalModel', $academicYear) === true,
            'canMigrate' => $user?->can('migrateInstructionalModel', $academicYear) === true
                && $this->migrateInstructionalModel->canBeMigrated($academicYear),
            'impacts' => $this->impactsFor($academicYear, $model),
            'migrations' => InstructionalModelMigration::where('school_id', $academicYear->school_id)
                ->where('academic_year_id', $academicYear->id)
                ->with('migratedBy')
                ->latest('id')
                ->get(),
        ]);
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

        return back()->with('success', "{$academicYear->name} now teaches with: {$model->label()}");
    }
}
