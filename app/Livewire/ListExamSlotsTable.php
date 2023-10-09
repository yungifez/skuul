<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ListExamSlotsTable extends Component
{
    public Exam $exam;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.list-exam-slots-table');
    }
}
