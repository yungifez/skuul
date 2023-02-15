<?php

namespace App\Http\Livewire;

use Livewire\Component;

class MarkTabulation extends Component
{
    public $tabulatedRecords;

    public $totalMarksAttainableInEachSubject;

    public $subjects;

    public $title = '';

    public function render()
    {
        return view('livewire.mark-tabulation');
    }
}
