<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowUserProfile extends Component
{
    public User $user;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.show-user-profile');
    }
}
