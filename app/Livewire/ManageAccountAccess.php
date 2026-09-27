<?php

namespace App\Livewire;

use App\Actions\Identity\ChangeAccountStatus;
use App\Actions\Identity\RevokeAccountInvitation;
use App\Actions\Identity\SendAccountInvitation;
use App\Enums\AccountStatus;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show whether a person can sign in, and invite, suspend, archive, or
 * reinstate them.
 */
class ManageAccountAccess extends Component
{
    use DispatchesStatusNotifications;

    public User $user;

    public function sendInvitation(SendAccountInvitation $sendAccountInvitation): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $sendAccountInvitation->send($this->user, auth()->user());

        $this->notify("Sent an invitation to {$this->user->email}.");
    }

    public function revokeInvitation(RevokeAccountInvitation $revokeAccountInvitation): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $revoked = $revokeAccountInvitation->revoke($this->user, auth()->user());

        $this->notify($revoked > 0
            ? "Revoked the invitation for {$this->user->name}."
            : "{$this->user->name} has no invitation to revoke.");
    }

    public function suspend(ChangeAccountStatus $changeAccountStatus): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $this->user = $changeAccountStatus->suspend($this->user, auth()->user());
        $this->announceStatus();
    }

    public function archive(ChangeAccountStatus $changeAccountStatus): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $this->user = $changeAccountStatus->archive($this->user, auth()->user());
        $this->announceStatus();
    }

    public function reinstate(ChangeAccountStatus $changeAccountStatus): void
    {
        Gate::authorize('manageAccountAccess', $this->user);

        $this->user = $changeAccountStatus->reinstate($this->user, auth()->user());
        $this->announceStatus();
    }

    public function render(): View
    {
        $status = $this->user->account_status;

        return view('livewire.manage-account-access', [
            'status' => $status,
            'isBlocked' => in_array($status, [AccountStatus::Suspended, AccountStatus::Archived], true),
            'hasPendingInvitation' => $this->user->pendingAccountInvitation() !== null,
            'canManage' => Gate::allows('manageAccountAccess', $this->user),
        ]);
    }

    private function announceStatus(): void
    {
        $this->notify("Set {$this->user->name}'s account to {$this->user->account_status->label()}.");
    }
}
