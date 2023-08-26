<?php

namespace App\Livewire;

use Livewire\Component;

class ShowSchool extends Component
{
    public $school;

    public function render()
    {
        return view('livewire.show-school');
    }
}
