<?php

namespace App\Livewire;

use App\Services\MyClass\MyClassService;
use Livewire\Component;

class ListSubjectsTable extends Component
{
    protected $queryString = ['class'];

    public $classes;

    public $class;

    public function mount(MyClassService $myClassService)
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

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
        return view('livewire.list-subjects-table');
    }
}
