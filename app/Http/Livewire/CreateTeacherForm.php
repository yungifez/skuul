<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateTeacherForm extends Component
{
    public bool $includeFormTag = true;

    public function render()
    {
        return view('livewire.create-teacher-form');
    }
}
