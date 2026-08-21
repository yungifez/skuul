<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Services\MyClass\MyClassService;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class ListSyllabiTable extends Component
{
    protected $queryString = ['class'];

    public $class;

    public $classes;

    public function mount(MyClassService $myClassService)
    {
        $this->setErrorBag(session()->get('errors', new MessageBag)->getMessages());

        if (auth()->user()->hasRole(Role::Student)) {
            return $this->class = auth()->user()->studentRecord->myClass->id;
        }

        $this->classes = $myClassService->getAllClasses();
        if ($this->classes->isNotEmpty()) {
            $this->updatedClass();
        }
    }

    public function updatedClass()
    {
        if ($this->classes->find($this->class) == null) {
            $this->class = $this->classes?->first()->id;
        }

        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.list-syllabi-table');
    }
}
