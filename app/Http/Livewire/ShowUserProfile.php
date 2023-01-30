<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowUserProfile extends Component
{
    public User $user;

    public function render()
    {
        return view('livewire.show-user-profile');
    }
}
