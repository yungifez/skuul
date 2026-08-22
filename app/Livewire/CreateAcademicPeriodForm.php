<?php

namespace App\Livewire;

use Illuminate\Support\MessageBag;
use Livewire\Component;

class CreateAcademicPeriodForm extends Component
{
    public function render()
    {
        $this->setErrorBag(session()->get('errors', new MessageBag())->getMessages());

        return view('livewire.create-academic-period-form');
    }
}
