<?php

namespace App\Livewire;

use Livewire\Component;

class CreateUserFields extends Component
{
    public string $role = 'User';

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }
    public function render()
    {
        return view('livewire.create-user-fields');
    }
}
