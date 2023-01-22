<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class ShowParentProfile extends Component
{
    public User $parent;

    public function render()
    {
        return view('livewire.show-parent-profile');
    }
}
