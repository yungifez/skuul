<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditUserFields extends Component
{
    public $role = 'User';

    public User $user;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-user-fields');
    }
}
