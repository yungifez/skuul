<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\Subject\SubjectService;

class ListSubjectsTable extends Component
{
    public function mount(SubjectService $subjectService)
    {
        $this->subjects = $subjectService->getAllSubjects()->load('myClass');
    }
    public function render()
    {
        return view('livewire.list-subjects-table');
    }
}
