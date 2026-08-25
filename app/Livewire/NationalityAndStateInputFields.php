<?php

namespace App\Livewire;

use Livewire\Component;
use Nnjeim\World\World;

class NationalityAndStateInputFields extends Component
{
    public $nationalities;

    public $country;

    /**
     * Legacy input name retained for the create and edit user forms.
     */
    public $nationality;

    public $states;

    public $state;

    protected $rules = [
        'country' => 'nullable|string',
        'nationality' => 'nullable|string',
        'state'       => 'nullable|string',
    ];

    public function mount()
    {
        // @phpstan-ignore-next-line
        $this->nationalities = World::countries()->data->pluck('name');

        $this->country = $this->country ?? $this->nationality;

        if ($this->country !== null && !in_array($this->country, $this->nationalities->all())) {
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
            'fields'  => 'states',
            'filters' => [
                'name' => $this->country,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->country]]);
        }
        $this->state = $this->states[0]['name'];

        $this->dispatch('country-updated', ['country' => $this->country]);
        $this->dispatch('state-updated', ['state' => $this->state]);
    }

    public function updatedNationality(): void
    {
        $this->country = $this->nationality;
        $this->updatedCountry();
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
            'fields'  => 'states',
            'filters' => [
                'name' => $this->country,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->country]]);
        }
        if ($this->state === null || !$this->states->pluck('name')->contains($this->state)) {
            $this->state = $this->states[0]['name'];
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
