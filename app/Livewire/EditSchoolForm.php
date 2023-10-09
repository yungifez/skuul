<?php

namespace App\Livewire;

use App\Models\School;
use Livewire\Component;

class EditSchoolForm extends Component
{
    public School $school;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-school-form');
    }
}
