<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowClass extends Component
{
    public $class;

    public $students;

    public function mount()
    {
        $this->class = $this->class;
    }

    public function render()
    {
        return view('livewire.show-class');
    }
}
