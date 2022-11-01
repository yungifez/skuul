<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CreateParentForm extends Component
{
    public bool $includeFormTag = true;

    public function render()
    {
        return view('livewire.create-parent-form');
    }
}
