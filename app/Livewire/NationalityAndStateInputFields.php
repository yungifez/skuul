<?php

namespace App\Livewire;

use Livewire\Component;
use Nnjeim\World\World;

class NationalityAndStateInputFields extends Component
{
    public $country;

    public $countries;

    public $states;

    public $state;

    protected $rules = [
        'country' => 'nullable|string',
        'state' => 'nullable|string',
    ];

    public function mount()
    {
        // @phpstan-ignore-next-line
        $this->countries = World::countries()->data->pluck('name');

        if ($this->country !== null && !in_array($this->country, $this->countries->all())) {
            $this->country = null;
        }
    }

    public function updatedCountry(): void
    {
        if (blank($this->country)) {
            $this->states = collect();
            $this->state = null;
            $this->dispatch('country-updated', ['country' => null]);
            $this->dispatch('state-updated', ['state' => null]);

            return;
        }

        $this->states = collect(World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->country,
            ],
        ])->data->pluck('states')->first());
        $this->state = $this->states->first()['name'] ?? null;

        $this->dispatch('country-updated', ['country' => $this->country]);
        $this->dispatch('state-updated', ['state' => $this->state]);
    }

    public function loadInitialStates(): void
    {
        if (blank($this->country)) {
            $this->states = collect();
            $this->state = null;

            $this->dispatch('country-updated', ['country' => null]);
            $this->dispatch('state-updated', ['state' => null]);

            return;
        }
        $this->states = collect(World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->country,
            ],
        ])->data->pluck('states')->first());
        if ($this->state === null || !$this->states->pluck('name')->contains($this->state)) {
            $this->state = $this->states->first()['name'] ?? null;
        }

        $this->dispatch('country-updated', ['country' => $this->country]);
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
