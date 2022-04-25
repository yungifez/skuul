<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AcademicYear;

class EditAcademicYearForm extends Component
{
    public AcademicYear $academicYear;
    public function render()
    {
        return view('livewire.edit-academic-year-form');
    }
}
