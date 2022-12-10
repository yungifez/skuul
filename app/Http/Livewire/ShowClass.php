<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowClass extends Component
{
    public $class;
    public $students;

    public function mount()
    {
        $this->class = $this->class->load('studentRecords','studentRecords.user', 'subjects', 'subjects.teachers');
        $this->students = $this->class->students();
    }

    public function render()
    {
        return view('livewire.show-class');
    }
}
