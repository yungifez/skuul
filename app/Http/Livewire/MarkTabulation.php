<?php

namespace App\Http\Livewire;

use Livewire\Component;

class MarkTabulation extends Component
{
    public $tabulatedRecords, $totalMarksAttainableInEachSubject, $subjects;
    public function render()
    {
        return view('livewire.mark-tabulation');
    }
}
