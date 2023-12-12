<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditUserFields extends Component
{
    public $role = 'User';

    public User $user;

    public function render()
    {
        return view('livewire.edit-user-fields');
    }
}
