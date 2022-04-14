<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AcademicYear;

class ShowAcademicYear extends Component
{
    public AcademicYear $academicYear;
    public function render()
    {
        return view('livewire.show-academic-year');
    }
}
