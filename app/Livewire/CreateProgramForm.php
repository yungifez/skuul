<?php

namespace App\Livewire;

use App\Actions\Cohort\SaveProgram;
use App\Enums\ProgramType;
use App\Exceptions\InvalidValueException;
use App\Models\Program;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Open a club, a support programme or any other activity learners take part in.
 */
class CreateProgramForm extends Component
{
    public string $name = '';

    public string $type = '';

    public string $description = '';

    public function mount(): void
    {
        Gate::authorize('create', Program::class);

        $this->type = ProgramType::Club->value;
    }

    public function save(SaveProgram $saveProgram): void
    {
        Gate::authorize('create', Program::class);

        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::enum(ProgramType::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [], ['type' => 'kind']);

        try {
            $program = $saveProgram->create([
                'name' => $this->name,
                'type' => $this->type,
                'description' => $this->description === '' ? null : $this->description,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The programme was opened.');
        $this->redirectRoute('programs.show', $program);
    }

    public function render(): View
    {
        return view('livewire.create-program-form', ['types' => ProgramType::cases()]);
    }
}
