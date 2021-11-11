<?php

namespace App\Http\Livewire;

use Livewire\Component;

class EditSchool extends Component
{
    public object $school;

    public function render()
    {
        return view('livewire.edit-school');
    }
}
