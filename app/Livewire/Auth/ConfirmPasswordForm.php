<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ConfirmPasswordForm extends Component
{
    public function render(): View
    {
        return view('livewire.auth.confirm-password-form');
    }
}
