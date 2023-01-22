<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class EditParentForm extends Component
{
    public User $parent;

    public function render()
    {
        return view('livewire.edit-parent-form');
    }
}
