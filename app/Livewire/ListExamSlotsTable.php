<?php

namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ListExamSlotsTable extends Component
{
    public Exam $exam;

    public function render()
    {
        return view('livewire.list-exam-slots-table');
    }
}
