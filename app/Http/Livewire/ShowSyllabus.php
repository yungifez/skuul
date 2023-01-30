<?php

namespace App\Http\Livewire;

use App\Models\Syllabus;
use Livewire\Component;

class ShowSyllabus extends Component
{
    public Syllabus $syllabus;

    public function render()
    {
        return view('livewire.show-syllabus');
    }
}
