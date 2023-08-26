<?php

namespace App\Livewire;

use Livewire\Component;

class EditSectionForm extends Component
{
    public $section;

    public function render()
    {
        return view('livewire.edit-section-form');
    }
}
