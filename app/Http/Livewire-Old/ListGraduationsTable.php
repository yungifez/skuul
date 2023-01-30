<?php

namespace App\Http\Livewire;

use App\Services\Student\StudentService;
use Livewire\Component;

class ListGraduationsTable extends Component
{
    public $students;

    public function mount(StudentService $studentService)
    {
        $this->students = $studentService->getAllGraduatedStudents();
    }

    public function render()
    {
        return view('livewire.list-graduations-table');
    }
}
