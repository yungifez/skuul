<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowNotice extends Component
{
    public $notice;

    public function render()
    {
        return view('livewire.show-notice');
    }
}
