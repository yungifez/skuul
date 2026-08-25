<?php

namespace App\Livewire;

use App\Models\School;
use Livewire\Component;
use Nnjeim\World\Models\Country;

class EditSchoolForm extends Component
{
    public School $school;

    public $countries;

    public function mount(): void
    {
        $this->countries = Country::query()->orderBy('name')->get(['name']);
    }

    public function render()
    {
        return view('livewire.edit-school-form');
    }
}
