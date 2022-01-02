<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Student\StudentService;

class ListStudentsTable extends Component
{
    public $students;

    public function mount(StudentService $studentService)
    {
        $this->students = $studentService->getAllStudents();
    }

    public function render()
    {
        return view('livewire.list-students-table');
    }
}
