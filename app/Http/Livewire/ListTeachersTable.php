<?php

namespace App\Http\Livewire;

use App\Services\Teacher\TeacherService;
use Livewire\Component;

class ListTeachersTable extends Component
{
    public $teachers;

    public function mount(TeacherService $teacherService)
    {
        $this->teachers = $teacherService->getAllteachers();
    }

    public function render()
    {
        return view('livewire.list-teachers-table');
    }
}
