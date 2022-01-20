<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class EditSubjectForm extends Component
{
    public object $subject;
    
    public function render()
    {
        return view('livewire.edit-subject-form');
    }
}
