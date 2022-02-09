<?php

namespace App\Http\Livewire;

use Livewire\Component;
use PragmaRX\Countries\Package\Countries;

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
        $this->nationalities = collect(Countries::all()->pluck('name.common'));

        if ($this->nationality != null && ! in_array($this->nationality, $this->nationalities->toArray())) {
            $this->nationality = null;
        }
    }

    public function updatedNationality()
    {
        $this->states = collect(Countries::where('name.common', $this->nationality)->first()->hydrateStates()->states->pluck('name'));
    }

    public function loadInitialStates()
    {
        if ($this->nationality != null) {
            $this->states = collect(Countries::where('name.common', $this->nationality)->first()->hydrateStates()->states->pluck('name'));
        } else {
            $this->states = collect(Countries::where('name.common', $this->nationalities->first())->first()->hydrateStates()->states->pluck('name'));
        }
    }

    public function render()
    {
        return view('livewire.nationality-and-state-input-fields');
    }
}
