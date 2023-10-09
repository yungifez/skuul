<?php

namespace App\Livewire;

use Livewire\Component;

class EditSectionForm extends Component
{
    public $section;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-section-form');
    }
}
