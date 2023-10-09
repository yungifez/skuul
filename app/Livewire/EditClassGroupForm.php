<?php

namespace App\Livewire;

use App\Models\ClassGroup;
use Livewire\Component;

class EditClassGroupForm extends Component
{
    public ClassGroup $classGroup;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-class-group-form');
    }
}
