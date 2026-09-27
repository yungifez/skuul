<?php

namespace App\Livewire;

use App\Actions\Graduation\ManageGraduationPlan;
use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use App\Models\GraduationPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Write what a learner must finish before the school lets them graduate.
 */
class CreateGraduationPlanForm extends Component
{
    public string $name = '';

    public string $cohortId = '';

    public string $description = '';

    public string $completionOperator = 'all';

    public string $requiredCount = '';

    public bool $usesCredits = false;

    public string $requiredCredits = '';

    public function mount(): void
    {
        Gate::authorize('create', GraduationPlan::class);
    }

    public function save(ManageGraduationPlan $manageGraduationPlan): void
    {
        Gate::authorize('create', GraduationPlan::class);

        $this->name = trim($this->name);
        $this->description = trim($this->description);
        $countsCredits = $this->usesCredits || $this->completionOperator === 'at_least_credits';

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'cohortId' => ['nullable', 'integer', Rule::exists((new Cohort)->getTable(), 'id')->where('school_id', current_school_id())],
            'description' => ['nullable', 'string', 'max:1000'],
            'completionOperator' => ['required', Rule::in(ManageGraduationPlan::OPERATORS)],
            'requiredCount' => [Rule::requiredIf($this->completionOperator === 'at_least'), 'nullable', 'integer', 'min:1', 'max:1000'],
            'requiredCredits' => [Rule::requiredIf($countsCredits), 'nullable', 'integer', 'min:1', 'max:1000'],
        ], [
            'requiredCredits.required' => 'A plan that counts credits must say how many are needed.',
            'requiredCount.required' => 'Say how many items are needed.',
        ], ['cohortId' => 'group', 'completionOperator' => 'rule', 'requiredCount' => 'number needed', 'requiredCredits' => 'credits needed']);

        try {
            $plan = $manageGraduationPlan->create([
                'name' => $this->name,
                'description' => $this->description === '' ? null : $this->description,
                'cohort_id' => $this->cohortId === '' ? null : (int) $this->cohortId,
                'completion_operator' => $this->completionOperator,
                'required_count' => $this->requiredCount === '' ? null : (int) $this->requiredCount,
                'uses_credits' => $this->usesCredits,
                'required_credits' => $this->requiredCredits === '' ? null : (int) $this->requiredCredits,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The plan was written. Add what a learner must finish.');
        $this->redirectRoute('graduation-plans.show', $plan);
    }

    public function render(): View
    {
        return view('livewire.create-graduation-plan-form', [
            'cohorts' => Cohort::query()->inSchool()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
