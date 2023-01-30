<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ListExamSlotsTable extends Component
{
    public Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function render()
    {
        return view('livewire.list-exam-slots-table');
    }
}
