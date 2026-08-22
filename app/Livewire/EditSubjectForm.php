<?php

namespace App\Livewire;

use Livewire\Component;

class EditSubjectForm extends Component
{
    public object $subject;

    public function render()
    {
        return view('livewire.edit-subject-form');
    }
}
