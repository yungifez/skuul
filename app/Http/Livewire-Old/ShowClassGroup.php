<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowClassGroup extends Component
{
    public $classGroup;

    public function mount()
    {
        $this->classGroup = $this->classGroup->load('classes');
    }

    public function render()
    {
        return view('livewire.show-class-group');
    }
}
