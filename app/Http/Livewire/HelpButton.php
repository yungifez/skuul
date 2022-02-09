<?php

namespace App\Http\Livewire;

use Livewire\Component;

class HelpButton extends Component
{
    public $target_id;

    public $text;

    public function render()
    {
        return view('livewire.help-button');
    }
}
