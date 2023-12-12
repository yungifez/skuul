<?php

namespace App\Livewire;

use App\Models\Semester;
use Livewire\Component;

class EditSemesterForm extends Component
{
    public Semester $semester;

    public function render()
    {
        return view('livewire.edit-semester-form');
    }
}
