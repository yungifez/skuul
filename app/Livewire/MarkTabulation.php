<?php

namespace App\Livewire;

use Livewire\Component;

class MarkTabulation extends Component
{
    public $tabulatedRecords;

    public $totalMarksAttainableInEachSubject;

    public $subjects;

    public $title = '';

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.mark-tabulation');
    }
}
