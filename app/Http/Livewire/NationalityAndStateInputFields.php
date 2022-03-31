<?php

namespace App\Http\Livewire;
use Nnjeim\World\World;

use Livewire\Component;

class NationalityAndStateInputFields extends Component
{
    public $nationalities;
    public $nationality;
    public $states;
    public $state;

    protected $rules = [
        'nationality' => 'string',
        'state' => 'string',
    ];

    public function mount()
    {
        $this->nationalities = World::countries()->data->pluck('name');

        if ($this->nationality != null && ! in_array($this->nationality, $this->nationalities->toArray())) {
            $this->nationality = null;
        }
    }

    public function updatedNationality()
    {
        // $this->states = collect(World::where('name.common' , $this->nationality)->first()->hydrateStates()->states->pluck('name'));
        $this->states =  World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->nationality,
            ]
        ])->data->pluck('states')->first();
        if ($this->states->isEmpty()) {
            return $this->states = [['name' => $this->nationality]];
        }
        $this->state = $this->states[0]['name'];

        $this->dispatchBrowserEvent('nationality-updated',['nationality' => $this->nationality]);
        $this->dispatchBrowserEvent('state-updated',['state' => $this->state]);
    }

    public function loadInitialStates()
    {
        if ($this->nationality != null) {
            // $this->states =  collect(World::where('name.common' , $this->nationality)->first()->hydrateStates()->states->pluck('name'));
            $this->states =  $this->states =  World::countries([
                'fields' => 'states',
                'filters' => [
                    'name' => $this->nationality,
                ]
            ])->data->pluck('states')->first();

        }else {
        $this->states =  World::countries([
            'fields' => 'states',
            'filters' => [
                'name' => $this->nationalities->first(),
            ]
        ])->data->pluck('states')->first();
        }
        
    }

    public function updatedState()
    {
        $this->dispatchBrowserEvent('state-updated',['state' => $this->state]);
    }

    public function render()
    {
        return view('livewire.nationality-and-state-input-fields');
    }
}
