<?php

namespace App\Http\Livewire;

use App\Models\AcademicYear;
use Livewire\Component;

class EditAcademicYearForm extends Component
{
    public AcademicYear $academicYear;

    public function render()
    {
        return view('livewire.edit-academic-year-form');
    }
}
