<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowAdminProfile extends Component
{
    public User $admin;

    public function render()
    {
        return view('livewire.show-admin-profile');
    }
}
