<?php

namespace App\Livewire;

use App\Actions\Fortify\PasswordValidationRules;
use App\Actions\Identity\SetAccountPassword;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Set a password for another person's account without seeing the old one.
 */
class ManageAccountPassword extends Component
{
    use DispatchesStatusNotifications;
    use PasswordValidationRules;

    public User $user;

    public bool $isOpen = false;

    public string $password = '';

    public string $password_confirmation = '';

    public bool $forceReset = false;

    public function mount(User $user): void
    {
        Gate::authorize('manageAccountAccess', $user);

        $this->user = $user;
        $this->forceReset = $user->password_change_required_at !== null;
    }

    public function save(SetAccountPassword $setAccountPassword): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $this->validate([
            'password' => $this->passwordRules(),
            'forceReset' => ['boolean'],
        ]);

        $setAccountPassword->set(
            user: $this->user,
            password: $this->password,
            forceReset: $this->forceReset,
            actor: auth()->user(),
        );

        $this->reset('password', 'password_confirmation', 'isOpen');
        $this->user->refresh();
        $this->notify($this->forceReset
            ? "Set {$this->user->name}'s password and required a change at next sign-in."
            : "Set {$this->user->name}'s password.");
    }

    public function render(): View
    {
        return view('livewire.manage-account-password');
    }
}
