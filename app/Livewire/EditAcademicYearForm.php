<?php

namespace App\Livewire;

use App\Models\AcademicYear;
use Livewire\Component;

class EditAcademicYearForm extends Component
{
    public AcademicYear $academicYear;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-academic-year-form');
    }
}
