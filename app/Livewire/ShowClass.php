<?php

namespace App\Livewire;

use Livewire\Component;

class ShowClass extends Component
{
    public $class;

    public $students;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());

        $this->class = $this->class;
    }

    public function render()
    {
        return view('livewire.show-class');
    }
}
