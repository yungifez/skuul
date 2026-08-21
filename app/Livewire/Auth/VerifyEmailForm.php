<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class VerifyEmailForm extends Component
{
    public function render(): View
    {
        return view('livewire.auth.verify-email-form');
    }
}
