<?php

namespace App\Livewire;

use Livewire\Component;

class ShowSection extends Component
{
    public $section;

    public $students;

    public function mount()
    {
        $this->section = $this->section->load('studentRecords', 'studentRecords.user');
        $this->students = $this->section->students();
    }

    public function render()
    {
        return view('livewire.show-section');
    }
}
