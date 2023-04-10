<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Nnjeim\World\World;

class NationalityAndStateInputFields extends Component
{
    public $nationalities;

    public $nationality;

    public $states;

    public $state;

    protected $rules = [
        'nationality' => 'string',
        'state'       => 'string',
    ];

    public function mount()
    {
        // @phpstan-ignore-next-line
        $this->nationalities = World::countries()->data->pluck('name');

        //set nationality to null if not found
        if ($this->nationality != null && !in_array($this->nationality, $this->nationalities->all())) {
            $this->nationality = null;
        }
    }

    public function updatedNationality()
    {
        // $this->states = collect(World::where('name.common' , $this->nationality)->first()->hydrateStates()->states->pluck('name'));
        $this->states = collect(World::countries([
            'fields'  => 'states',
            'filters' => [
                'name' => $this->nationality,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->nationality]]);
        }
        $this->state = $this->states[0]['name'];

        $this->dispatchBrowserEvent('nationality-updated', ['nationality' => $this->nationality]);
        $this->dispatchBrowserEvent('state-updated', ['state' => $this->state]);
    }

    public function loadInitialStates()
    {
        if ($this->nationality == null) {
            $this->nationality = $this->nationalities->first();
        }
        $this->states = collect(World::countries([
            'fields'  => 'states',
            'filters' => [
                'name' => $this->nationality,
            ],
        ])->data->pluck('states')->first());
        if ($this->states->isEmpty()) {
            $this->states = collect([['name' => $this->nationality]]);
        }
        if ($this->state == null || in_array($this->state, $this->states->all())) {
            $this->state = $this->states[0]['name'];
        }

        $this->dispatchBrowserEvent('nationality-updated', ['nationality' => $this->nationality]);
        $this->dispatchBrowserEvent('state-updated', ['state' => $this->state]);
    }

    public function updatedState()
    {
        $this->dispatchBrowserEvent('state-updated', ['state' => $this->state]);
    }

    public function render()
    {
        return view('livewire.nationality-and-state-input-fields');
    }
}
