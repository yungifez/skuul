<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditAdminForm extends Component
{
    public User $admin;

    public function render()
    {
        return view('livewire.edit-admin-form');
    }
}
