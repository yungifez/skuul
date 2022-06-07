<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;

class CreateExamSlotForm extends Component
{
    public Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function render()
    {
        return view('livewire.create-exam-slot-form');
    }
}
