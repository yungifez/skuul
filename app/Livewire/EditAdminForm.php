<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EditAdminForm extends Component
{
    public User $admin;

    public function mount()
    {
        $this->setErrorBag(session()->get('errors', new \Illuminate\Support\MessageBag())->getMessages());
    }

    public function render()
    {
        return view('livewire.edit-admin-form');
    }
}
