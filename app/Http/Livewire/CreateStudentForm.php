<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateStudentForm extends Component
{
    public $school = null;
    public bool $includeFormTag = true;

    public function render()
    {
        return view('livewire.create-student-form');
    }
}
