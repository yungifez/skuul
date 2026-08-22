<?php

namespace App\Livewire;

use App\Services\Teacher\TeacherService;
use Illuminate\View\View;
use Livewire\Component;

class CreateSubjectForm extends Component
{
    public $teachers;

    public function mount(TeacherService $teacherService): void
    {
        $this->teachers = $teacherService->getAllTeachers();
    }

    public function render(): View
    {
        return view('livewire.create-subject-form');
    }
}
