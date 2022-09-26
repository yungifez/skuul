<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowClass extends Component
{
    public $class;

    public function mount()
    {
        $this->class = $this->class->load('studentRecords');
    }

    public function render()
    {
        return view('livewire.show-class');
    }
}
