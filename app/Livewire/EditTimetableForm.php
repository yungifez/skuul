<?php

namespace App\Livewire;

use App\Models\Timetable;
use Illuminate\View\View;
use Livewire\Component;

class EditTimetableForm extends Component
{
    public Timetable $timetable;

    public function mount(): void
    {
        $this->timetable->loadMissing('academicCycleSection.academicLevel');
    }

    public function render(): View
    {
        return view('livewire.edit-timetable-form');
    }
}
