<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowTeacherProfile extends Component
{
    public User $teacher;

    public function render()
    {
        return view('livewire.show-teacher-profile');
    }
}
