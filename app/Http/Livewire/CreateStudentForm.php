<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Services\MyClass\MyClassService;
use App\Services\Section\SectionService;
use PragmaRX\Countries\Package\Countries;

class CreateStudentForm extends Component
{
    public $countries;
    public $country;
    public $states;
    public $state;
    public $myClasses;
    public $myClass;
    public $sections;
    public $section;
    protected $myClassService;

    public function mount(MyClassService $myClassService)
    {
        $this->countries = collect(Countries::all()->pluck('name.common'));
        $this->myClasses = $myClassService->getAllClasses();
    }

    public function updatedCountry()
    {
        $this->reset('state');
        // dd($this->myClasses);
        $this->states = collect(Countries::where('name.common' , $this->country)->first()->hydrateStates()->states->pluck('name')); 
        // dd($this->states);
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
