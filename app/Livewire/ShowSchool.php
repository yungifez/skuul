<?php

namespace App\Livewire;

use App\Models\School;
use Illuminate\View\View;
use Livewire\Component;

class ShowSchool extends Component
{
    public School $school;

    public function mount(School $school): void
    {
        $this->school = $school->load([
            'organization:id,name',
            'academicYear:id,school_id,start_year,stop_year,starts_on,ends_on,status',
            'academicPeriod:id,academic_year_id,name,starts_on,ends_on,status',
        ]);
    }

    public function render(): View
    {
        return view('livewire.show-school');
    }
}
