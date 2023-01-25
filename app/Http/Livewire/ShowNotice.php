<?php

namespace App\Http\Livewire;

use App\Models\Notice;
use Livewire\Component;

class ShowNotice extends Component
{
    public Notice $notice;

    public function render()
    {
        return view('livewire.show-notice');
    }
}
