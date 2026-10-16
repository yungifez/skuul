<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use Livewire\Component;

class EditAcademicPeriodForm extends Component
{
    public AcademicPeriod $academicPeriod;

    public function render()
    {
        return view('livewire.edit-academic-period-form');
    }
}
