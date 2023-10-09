<?php

namespace App\Livewire;

use App\Models\Notice;
use Livewire\Component;

class ShowNotice extends Component
{
    public Notice $notice;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.show-notice');
    }
}
