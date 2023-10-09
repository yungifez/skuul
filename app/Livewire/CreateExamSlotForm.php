<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class CreateExamSlotForm extends Component
{
    public Exam $exam;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }
    public function render()
    {
        return view('livewire.create-exam-slot-form');
    }
}
