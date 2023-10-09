<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowTeacherProfile extends Component
{
    public User $teacher;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.show-teacher-profile');
    }
}
