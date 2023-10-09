<?php

namespace App\Livewire;

use App\Models\Semester;
use Livewire\Component;

class EditSemesterForm extends Component
{
    public Semester $semester;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-semester-form');
    }
}
