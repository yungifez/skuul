<?php

namespace App\Livewire;

use Livewire\Component;

class ShowSchool extends Component
{
    public $school;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.show-school');
    }
}
