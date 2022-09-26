<?php

namespace App\Http\Livewire;

use Livewire\Component;

class EditClassGroupForm extends Component
{
    public $classGroup;

    public function render()
    {
        return view('livewire.edit-class-group-form');
    }
}
