<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DropdownLinks extends Component
{
    public $button_label = 'actions';

    public $links;

    public $forms;

    public function render()
    {
        return view('livewire.dropdown-links');
    }
}
