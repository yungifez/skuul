<?php

namespace App\Http\Livewire;

use Livewire\Component;

class EditSchoolForm extends Component
{
    public object $school;

    public function render()
    {
        return view('livewire.edit-school-form');
    }
}
