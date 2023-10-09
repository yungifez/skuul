<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowAdminProfile extends Component
{
    public User $admin;

    function mount() {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag)->getMessages());
    }

    public function render()
    {
        return view('livewire.show-admin-profile');
    }
}
