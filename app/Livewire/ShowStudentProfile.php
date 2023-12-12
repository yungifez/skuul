<?php

namespace App\Livewire;

use App\Models\StudentRecord;
use App\Models\User;
use Livewire\Component;

class ShowStudentProfile extends Component
{
    public User $student;

    public StudentRecord $studentRecord;

    public function mount()
    {
        $this->student = $this->student->loadMissing('studentRecord');
        $this->studentRecord = $this->student->studentRecord()->withoutGlobalScopes()->first();
    }

    public function render()
    {
        return view('livewire.show-student-profile');
    }
}
