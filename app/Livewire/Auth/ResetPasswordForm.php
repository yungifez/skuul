<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;

class ResetPasswordForm extends Component
{
    public string $email = '';

    public string $token = '';

    public function mount(Request $request): void
    {
        $this->email = (string) $request->input('email');
        $this->token = (string) $request->route('token');
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password-form');
    }
}
