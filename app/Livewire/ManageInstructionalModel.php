<?php

namespace App\Livewire;

use App\Actions\Curriculum\GrantOfferingException;
use App\Actions\Curriculum\MigrateInstructionalModel;
use App\Actions\Curriculum\SetInstructionalModel;
use App\Enums\InstructionalModel;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelException;
use App\Models\InstructionalModelMigration;
use App\Models\InstructionalModelSetting;
use App\Models\Subject;
use App\Services\Curriculum\InstructionalModelResolver;
use App\Services\Curriculum\OfferingExceptions;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Answer how a campus teaches one school year, and record where a subject differs.
 *
 * A year that has not started takes the answer directly. A running year moves
 * only through a recorded mid-year move, and an exception lets one subject
 * teach outside the answer without moving it.
 */
class ManageInstructionalModel extends Component
{
    public AcademicYear $academicYear;

    public bool $setup = false;

    public string $model = '';

    public string $reason = '';

    public string $moveTo = '';

    public string $moveReason = '';

    public bool $confirmMove = false;

    public ?int $exceptionSubjectId = null;

    public string $exceptionRosterMode = '';

    public ?int $exceptionLevelId = null;

    public string $exceptionReason = '';

    public function mount(AcademicYear $academicYear, bool $setup = false): void
    {
        Gate::authorize('viewInstructionalModel', $academicYear);

        $this->academicYear = $academicYear;
        $this->setup = $setup;
        $this->model = $this->currentModel()->value;
        $this->exceptionSubjectId = $this->subjects()->first()?->id;
        $this->exceptionRosterMode = $this->exceptionModes()[0]->value ?? '';
    }

    /**
     * Record the answer for a year that has not started.
     */
    public function save(SetInstructionalModel $setInstructionalModel): void
    {
        Gate::authorize('setInstructionalModel', $this->academicYear);

        $this->validate([
            'model' => ['required', Rule::enum(InstructionalModel::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ], ['model.required' => 'Answer the question about class groups.']);

        $model = InstructionalModel::from($this->model);

        try {
            $setInstructionalModel->set($this->academicYear, $model, auth()->user(), $this->reason === '' ? null : $this->reason);
        } catch (InvalidValueException $exception) {
            $this->addError('model', $exception->getMessage());

            return;
        }

        if ($this->setup) {
            session()->flash('success', 'Teaching approach saved. Now build this year’s classes.');
            $this->redirectRoute('academic-years.setup', [$this->academicYear, 'structure']);

            return;
        }

        session()->flash('success', "{$this->academicYear->name} now teaches with: {$model->label()}");
        $this->redirectRoute('academic-years.instructional-model.edit', $this->academicYear);
    }

    /**
     * Move a running year to another answer, and record why.
     */
    public function migrate(MigrateInstructionalModel $migrateInstructionalModel): void
    {
        Gate::authorize('migrateInstructionalModel', $this->academicYear);

        $this->validate([
            'moveTo' => ['required', Rule::enum(InstructionalModel::class)],
            'moveReason' => ['required', 'string', 'min:15', 'max:500'],
            'confirmMove' => ['accepted'],
        ], [
            'moveTo.required' => 'Choose the model this cycle is moving to.',
            'moveReason.required' => 'Say why this cycle is moving mid-year.',
            'moveReason.min' => 'Write the reason in a full sentence, so it reads later.',
            'confirmMove.accepted' => 'Confirm that you understand what changes for staff.',
        ]);

        $model = InstructionalModel::from($this->moveTo);

        try {
            $migrateInstructionalModel->migrate($this->academicYear, $model, $this->moveReason, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('moveTo', $exception->getMessage());

            return;
        }

        session()->flash('success', "{$this->academicYear->name} now teaches with: {$model->label()}");
        $this->redirectRoute('academic-years.instructional-model.edit', $this->academicYear);
    }

    /**
     * Let one subject be taught outside the campus answer.
     */
    public function grantException(GrantOfferingException $grantException): void
    {
        Gate::authorize('setInstructionalModel', $this->academicYear);

        $ofThisSchool = fn (Builder $query) => $query->where('school_id', $this->academicYear->school_id);

        $this->validate([
            'exceptionSubjectId' => ['required', 'integer', Rule::exists('subjects', 'id')->where($ofThisSchool)],
            'exceptionLevelId' => ['nullable', 'integer', Rule::exists('academic_levels', 'id')->where($ofThisSchool)->where('is_group', false)],
            'exceptionRosterMode' => ['required', Rule::in(RosterMode::values())],
            'exceptionReason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'exceptionSubjectId.required' => 'Choose the subject.',
            'exceptionReason.required' => 'Say why this subject is taught differently.',
            'exceptionReason.min' => 'Give a reason somebody can understand next year.',
        ]);

        try {
            $grantException->grant(
                academicYear: $this->academicYear,
                subject: Subject::inSchool($this->academicYear->school_id)->findOrFail($this->exceptionSubjectId),
                rosterMode: RosterMode::from($this->exceptionRosterMode),
                reason: $this->exceptionReason,
                academicLevel: $this->exceptionLevelId === null
                    ? null
                    : AcademicLevel::inSchool($this->academicYear->school_id)->findOrFail($this->exceptionLevelId),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('exceptionSubjectId', $exception->getMessage());

            return;
        }

        $this->reset('exceptionReason', 'exceptionLevelId');
    }

    /**
     * Take an exception back. Classes already running are left alone.
     */
    public function revokeException(int $exceptionId, GrantOfferingException $grantException): void
    {
        Gate::authorize('setInstructionalModel', $this->academicYear);

        $exception = InstructionalModelException::query()
            ->where('school_id', $this->academicYear->school_id)
            ->where('academic_year_id', $this->academicYear->id)
            ->findOrFail($exceptionId);

        try {
            $grantException->revoke($exception);
        } catch (InvalidValueException $invalid) {
            $this->addError('exceptions', $invalid->getMessage());
        }
    }

    public function setting(): ?InstructionalModelSetting
    {
        return app(InstructionalModelResolver::class)->settingFor($this->academicYear)?->loadMissing('updatedBy');
    }

    public function currentModel(): InstructionalModel
    {
        return $this->setting()->model ?? InstructionalModel::default();
    }

    /**
     * @return Collection<int, Subject>
     */
    public function subjects(): Collection
    {
        return Subject::inSchool($this->academicYear->school_id)->orderBy('name')->get();
    }

    /**
     * The rosters the current answer does not offer, so an exception can grant them.
     *
     * @return list<RosterMode>
     */
    public function exceptionModes(): array
    {
        $model = $this->currentModel();

        return array_values(array_filter(
            RosterMode::cases(),
            fn (RosterMode $mode): bool => !$model->allowsRosterMode($mode),
        ));
    }

    public function render(SetInstructionalModel $setInstructionalModel, MigrateInstructionalModel $migrateInstructionalModel, OfferingExceptions $exceptions): View
    {
        $user = auth()->user();
        $setting = $this->setting();
        $model = $setting->model ?? InstructionalModel::default();
        $canMigrate = $user->can('migrateInstructionalModel', $this->academicYear)
            && $migrateInstructionalModel->canBeMigrated($this->academicYear);

        return view('livewire.manage-instructional-model', [
            'setting' => $setting,
            'currentModel' => $model,
            'isFutureCycle' => $setInstructionalModel->isFutureCycle($this->academicYear),
            'canSet' => $user->can('setInstructionalModel', $this->academicYear),
            'canMigrate' => $canMigrate,
            'impacts' => $canMigrate ? collect(InstructionalModel::cases())
                ->reject(fn (InstructionalModel $option): bool => $option === $model)
                ->mapWithKeys(fn (InstructionalModel $option): array => [$option->value => $migrateInstructionalModel->impactOf($this->academicYear, $option)])
                ->all() : [],
            'migrations' => InstructionalModelMigration::where('school_id', $this->academicYear->school_id)
                ->where('academic_year_id', $this->academicYear->id)
                ->with('migratedBy')
                ->latest('id')
                ->get(),
            'exceptions' => $exceptions->forCycle($this->academicYear),
            'subjects' => $this->subjects(),
            'academicLevels' => AcademicLevel::inSchool($this->academicYear->school_id)->where('is_group', false)->orderBy('position')->orderBy('name')->get(),
            'exceptionModes' => $this->exceptionModes(),
        ]);
    }
}
