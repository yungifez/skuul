<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\ExamSlot;
use Livewire\Component;

class EditExamSlotForm extends Component
{
    public Exam $exam;

    public ExamSlot $examSlot;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-exam-slot-form');
    }
}
