<?php

namespace App\Http\Livewire;

use Livewire\Component;
use PragmaRX\Countries\Package\Countries;

class CreateUserFields extends Component
{
    public string $role = 'user';
    public $countries;
    public $country;
    public $states;

    protected $rules = [
        'role' => 'string',
        'country' => 'string',
    ];
 

    public function mount()
    {
        $this->countries = collect(Countries::all()->pluck('name.common'));
    }
    public function updatedCountry()
    {
        $this->states = collect(Countries::where('name.common' , $this->country)->first()->hydrateStates()->states->pluck('name')); 
    }

    public function render()
    {
        return view('livewire.create-user-fields');
    }
}
