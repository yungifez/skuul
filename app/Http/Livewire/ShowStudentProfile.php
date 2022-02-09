<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowStudentProfile extends Component
{
    public User $student;

    public function render()
    {
        return view('livewire.show-student-profile');
    }
}
