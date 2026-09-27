<?php

namespace App\Livewire;

use App\Actions\Cohort\SaveCohort;
use App\Enums\CohortType;
use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Make a named group of people that is not a class and not a section.
 */
class CreateCohortForm extends Component
{
    public string $name = '';

    public string $type = '';

    public string $description = '';

    public function mount(): void
    {
        Gate::authorize('create', Cohort::class);

        $this->type = CohortType::GraduationYear->value;
    }

    public function save(SaveCohort $saveCohort): void
    {
        Gate::authorize('create', Cohort::class);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::enum(CohortType::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [], ['type' => 'kind of group']);

        try {
            $cohort = $saveCohort->create([
                'name' => $this->name,
                'type' => $this->type,
                'description' => $this->description === '' ? null : $this->description,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The group was made.');
        $this->redirectRoute('cohorts.show', $cohort);
    }

    public function render(): View
    {
        return view('livewire.create-cohort-form', ['types' => CohortType::cases()]);
    }
}
