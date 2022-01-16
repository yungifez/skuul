<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;

class CreateStudentForm extends Component
{
    public $myClasses;
    public $myClass;
    public $sections;
    public $section;
    protected $myClassService;

    protected $rules = [
        'myClass' => 'string',
        'section' => 'string',
    ];
 

    public function mount(MyClassService $myClassService)
    {
        $this->myClasses = $myClassService->getAllClasses();
    }


    public function updatedMyClass()
    {
        $this->reset('section');
        $this->sections = collect(App::make(MyClassService::class)->getClassById($this->myClass)->sections);
    }

    public function render()
    {
        return view('livewire.create-student-form');
    }
}
