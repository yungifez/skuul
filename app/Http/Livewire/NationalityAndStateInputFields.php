<?php

namespace App\Http\Livewire;
use PragmaRX\Countries\Package\Countries;


use Livewire\Component;

class NationalityAndStateInputFields extends Component
{
    public $nationalities;
    public $nationality;
    public $states;
    public $state;

    protected $rules = [
        'nationality' => 'string',
        'state' => 'string'
    ];
 

    public function mount()
    {
        $this->nationalities = collect(Countries::all()->pluck('name.common'));
    }
    public function updatedNationality()
    {
        $this->states = collect(Countries::where('name.common' , $this->nationality)->first()->hydrateStates()->states->pluck('name'));
    }
    public function loadInitialStates()
    {
        if ($this->nationality != null) {
            $this->states =  collect(Countries::where('name.common' , $this->nationality)->first()->hydrateStates()->states->pluck('name'));
        }else {
              $this->states =  collect(Countries::where('name.common' , $this->nationalities->first())->first()->hydrateStates()->states->pluck('name'));
        }
      
    }

    public function render()
    {
        return view('livewire.nationality-and-state-input-fields');
    }
}
