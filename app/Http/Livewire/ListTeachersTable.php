<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Teacher\TeacherService;

class ListTeachersTable extends Component
{
    public $teacher;

    public function mount(TeacherService $teacherService)
    {
        $this->teachers = $teacherService->getAllteachers();
    }
    
    public function render()
    {
        return view('livewire.list-teachers-table');
    }
}
