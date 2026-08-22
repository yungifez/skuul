<?php

namespace App\Http\Controllers;

use App\Actions\Curriculum\SetInstructionalModel;
use App\Enums\InstructionalModel;
use App\Http\Requests\SetInstructionalModelRequest;
use App\Models\AcademicYear;
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

        return view('pages.instructional-model.edit', [
            'academicYear'  => $academicYear,
            'setting'       => $setting?->loadMissing('updatedBy'),
            'model'         => $setting === null ? InstructionalModel::default() : $setting->model,
            'isFutureCycle' => $this->setInstructionalModel->isFutureCycle($academicYear),
            'canSet'        => $academicYear->exists && request()->user()?->can('setInstructionalModel', $academicYear) === true,
        ]);
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
