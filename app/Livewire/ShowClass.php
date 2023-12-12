<?php

namespace App\Livewire;

use Livewire\Component;

class ShowClass extends Component
{
    public $class;

    public $students;

    public function render()
    {
        return view('livewire.show-class');
    }
}
