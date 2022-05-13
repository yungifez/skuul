<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;
use App\Models\ExamSlot;

class EditExamSlotForm extends Component
{
    public Exam $exam;
    public ExamSlot $examSlot;
    public function render()
    {
        return view('livewire.edit-exam-slot-form');
    }
}
