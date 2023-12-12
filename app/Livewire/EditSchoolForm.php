<?php

namespace App\Livewire;

use App\Models\School;
use Livewire\Component;

class EditSchoolForm extends Component
{
    public School $school;

    public function render()
    {
        return view('livewire.edit-school-form');
    }
}
