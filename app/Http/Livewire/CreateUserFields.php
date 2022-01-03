<?php

namespace App\Http\Livewire;

use Livewire\Component;
use PragmaRX\Countries\Package\Countries;

class CreateUserFields extends Component
{
    public $countries;
    public $country;
    public $states;
    public $state;

    public function mount()
    {
        $this->countries = collect(Countries::all()->pluck('name.common'));
    }
    public function updatedCountry()
    {
        $this->reset('state');
        // dd($this->myClasses);
        $this->states = collect(Countries::where('name.common' , $this->country)->first()->hydrateStates()->states->pluck('name')); 
        // dd($this->states);
    }

    public function render()
    {
        return view('livewire.create-user-fields');
    }
}
