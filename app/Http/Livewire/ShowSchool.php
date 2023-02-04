<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowSchool extends Component
{
    public $school;

    public function render()
    {
        return view('livewire.show-school');
    }
}
