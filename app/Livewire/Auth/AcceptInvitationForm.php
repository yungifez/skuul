<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AcceptInvitationForm extends Component
{
    /**
     * The plain, one-time invitation token from the link.
     */
    public string $token = '';

    /**
     * The email address the invitation was sent to.
     */
    public string $email = '';

    public function mount(string $token, User $user): void
    {
        $this->token = $token;
        $this->email = $user->email;
    }

    public function render(): View
    {
        return view('livewire.auth.accept-invitation-form');
    }
}
