<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class EditTeacherForm extends Component
{
    public User $teacher;

    public function render()
    {
        return view('livewire.edit-teacher-form');
    }
}
