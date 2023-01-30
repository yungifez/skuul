<?php

namespace App\Http\Livewire;

use App\Models\ClassGroup;
use Livewire\Component;

class ShowClassGroup extends Component
{
    public ClassGroup $classGroup;

    public function render()
    {
        return view('livewire.show-class-group');
    }
}
