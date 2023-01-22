<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Student\StudentService;

class ShowPromotion extends Component
{
    public $promotion,$students;

    public function mount(StudentService $studentService)
    {
        $this->students = $studentService->getStudentById($this->promotion->students);
    }
    public function render()
    {
        return view('livewire.show-promotion');
    }
}
