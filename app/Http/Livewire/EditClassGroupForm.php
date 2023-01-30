<?php

namespace App\Http\Livewire;

use App\Models\ClassGroup;
use Livewire\Component;

class EditClassGroupForm extends Component
{
    public ClassGroup $classGroup;

    public function render()
    {
        return view('livewire.edit-class-group-form');
    }
}
