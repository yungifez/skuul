<?php

namespace App\Livewire;

use App\Services\Student\StudentService;
use Livewire\Component;

class ShowPromotion extends Component
{
    public $promotion;

    public $students;

    public function mount(StudentService $studentService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());

        $this->students = $studentService->getStudentById($this->promotion->students);
    }

    public function render()
    {
        return view('livewire.show-promotion');
    }
}
