<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\MyClass\MyClassService;

class EditSubjectForm extends Component
{
    public object $subject;
    public $classesOfferingSubject;

    public function mount(object $subject,MyClassService $classService)
    {
        $this->subject = $subject;
        $classesOfferingSubject = $subject->classes->pluck('id')->toArray();
        
        $this->classes = $classService->getAllClasses();

        foreach ($this->classes as $class ) {
            if(in_array($class->id,$classesOfferingSubject)){
                $class->selected = true;
            }
        }
    }
    
    public function render()
    {
        return view('livewire.edit-subject-form');
    }
}
