<?php

namespace App\Livewire;

use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class CreateSyllabusForm extends Component
{
    /** @var Collection<int, CourseOffering> */
    public Collection $courseOfferings;

    public function mount(): void
    {
        $this->courseOfferings = CourseOffering::inSchool()
            ->with(['subject:id,name,short_name', 'academicPeriod:id,name,label', 'academicLevel:id,name,label'])
            ->orderByDesc('academic_year_id')
            ->orderBy('academic_level_id')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.create-syllabus-form');
    }
}
