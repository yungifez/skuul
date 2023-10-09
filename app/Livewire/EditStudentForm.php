<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditStudentForm extends Component
{
    public User $student;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-student-form');
    }
}
