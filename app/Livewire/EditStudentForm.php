<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditStudentForm extends Component
{
    public User $student;

    public function render()
    {
        return view('livewire.edit-student-form');
    }
}
