<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TwoFactorChallengeForm extends Component
{
    public function render(): View
    {
        return view('livewire.auth.two-factor-challenge-form');
    }
}
