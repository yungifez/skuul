<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Semester;

class EditSemesterForm extends Component
{
    public Semester $semester;
    public function render()
    {
        return view('livewire.edit-semester-form');
    }
}
