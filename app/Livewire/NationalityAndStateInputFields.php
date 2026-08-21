<?php

namespace App\Livewire;

use Livewire\Component;
use Nnjeim\World\World;

class NationalityAndStateInputFields extends Component
{
    public $nationalities;

    public $nationality;

    public $states;

    public $state;

    protected $rules = [
        'nationality' => 'nullable|string',
        'state' => 'nullable|string',
    ];

    public function mount()
    {
        // @phpstan-ignore-next-line
        $this->nationalities = World::countries()->data->pluck('name');

        // set nationality to null if not found
        if ($this->nationality != null && !in_array($this->nationality, $this->nationalities->all())) {
            $this->nationality = null;
        }
    }

    public function updatedNationality()
    {
        if (blank($this->nationality)) {
            $this->states = collect();
            $this->state = null;
            $this->dispatch('nationality-updated', ['nationality' => null]);
            $this->dispatch('state-updated', ['state' => null]);

            return;
        }

        $this->states = collect(World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->nationality,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->nationality]]);
        }
        $this->state = $this->states[0]['name'];

        $this->dispatch('nationality-updated', ['nationality' => $this->nationality]);
        $this->dispatch('state-updated', ['state' => $this->state]);
    }

    public function loadInitialStates()
    {
        if (blank($this->nationality)) {
            $this->states = collect();
            $this->state = null;

            return;
        }
        $this->states = collect(World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->nationality,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->nationality]]);
        }
        if ($this->state === null || !$this->states->pluck('name')->contains($this->state)) {
            $this->state = $this->states[0]['name'];
        }

        $this->dispatch('nationality-updated', ['nationality' => $this->nationality]);
        $this->dispatch('state-updated', ['state' => $this->state]);
    }

    public function updatedState()
    {
        $this->dispatch('state-updated', ['state' => $this->state]);
    }

    public function render()
    {
        return view('livewire.nationality-and-state-input-fields');
    }
}
