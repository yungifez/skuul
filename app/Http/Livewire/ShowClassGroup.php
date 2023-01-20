<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ClassGroup;

class ShowClassGroup extends Component
{
    public ClassGroup $classGroup;
    public function render()
    {
        return view('livewire.show-class-group');
    }
}
