<?php

namespace App\Http\Livewire;

use App\Services\Exam\ExamService;
use Livewire\Component;

class ListExamsTable extends Component
{
    public function mount(ExamService $examService)
    {
        $this->exams = $examService->getAllExamsInSemester(auth()->user()->school->semester_id);
    }

    public function render()
    {
        return view('livewire.list-exams-table');
    }
}
