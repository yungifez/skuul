<?php

namespace App\Livewire;

use Livewire\Component;

class CreateSemesterForm extends Component
{
    public function render()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());

        return view('livewire.create-semester-form');
    }
}
