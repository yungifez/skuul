<?php

namespace App\Livewire;

use Livewire\Component;

class CreateUserFields extends Component
{
    public string $role = 'User';

    public function render()
    {
        return view('livewire.create-user-fields');
    }
}
