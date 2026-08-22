<?php

namespace App\Livewire;

use App\Actions\Identity\RevokeAccountInvitation;
use App\Actions\Identity\SendAccountInvitation;
use App\Enums\AccountInvitationStatus;
use App\Models\AccountInvitation;
use App\Models\User;
use App\Services\Identity\AccountInvitationVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

/**
 * Manage the invitation links issued for people's sign-in accounts.
 *
 * The screen reads only the invitations the administrator is responsible for.
 * The token, the email, and the expiry stay in the identity actions; this
 * component asks them to work and reports what happened.
 */
class ListAccountInvitations extends Component
{
    use WithPagination;

    /**
     * The state tab being read.
     */
    #[Url]
    public string $status = 'pending';

    /**
     * The name or email address being searched for.
     */
    #[Url]
    public string $search = '';

    /**
     * What the last action did, shown at the top of the screen.
     */
    public ?string $feedback = null;

    protected AccountInvitationVisibility $visibility;

    public function boot(AccountInvitationVisibility $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function mount(): void
    {
        Gate::authorize('viewAny', AccountInvitation::class);

        if (AccountInvitationStatus::tryFrom($this->status) === null) {
            $this->status = AccountInvitationStatus::Pending->value;
        }
    }

    /**
     * Read the tab the administrator is on.
     */
    public function currentStatus(): AccountInvitationStatus
    {
        return AccountInvitationStatus::tryFrom($this->status) ?? AccountInvitationStatus::Pending;
    }

    /**
     * Open one state tab.
     */
    public function selectStatus(string $status): void
    {
        if (AccountInvitationStatus::tryFrom($status) === null) {
            return;
        }

        $this->status = $status;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Email a new link to the invited person.
     *
     * The identity action issues the new token and stops the old one, so this
     * screen never touches the token or the expiry itself.
     */
    public function resend(int $invitationId, SendAccountInvitation $sendAccountInvitation): void
    {
        $this->feedback = null;
        $this->resetErrorBag('invitations');

        $invitation = AccountInvitation::query()->with('user')->findOrFail($invitationId);

        Gate::authorize('resend', $invitation);

        try {
            $sendAccountInvitation->send($invitation->user, auth()->user());
        } catch (RuntimeException $exception) {
            $this->addError('invitations', $exception->getMessage());

            return;
        }

        $this->feedback = "Sent a new invitation to {$invitation->user->email}. The link that came before it no longer works.";
    }

    /**
     * Stop the unused link for the invited person.
     */
    public function revoke(int $invitationId, RevokeAccountInvitation $revokeAccountInvitation): void
    {
        $this->feedback = null;
        $this->resetErrorBag('invitations');

        $invitation = AccountInvitation::query()->with('user')->findOrFail($invitationId);

        Gate::authorize('revoke', $invitation);

        $revokeAccountInvitation->revoke($invitation->user, auth()->user());

        $this->feedback = "Revoked the invitation for {$invitation->user->name}. The link no longer works.";
    }

    public function render()
    {
        $invitations = $this->invitations();

        return view('livewire.list-account-invitations', [
            'invitations' => $invitations,
            'rows' => $this->rowsFor($invitations),
            'counts' => $this->counts(),
            'tabs' => AccountInvitationStatus::tabs(),
        ]);
    }

    /**
     * Read one page of the invitations in the open tab.
     *
     * @return LengthAwarePaginator<int, AccountInvitation>
     */
    private function invitations(): LengthAwarePaginator
    {
        return $this->visibleQuery()
            ->withStatus($this->currentStatus())
            ->with(['user.schoolMemberships.school', 'invitedBy'])
            ->latest('created_at')
            ->latest('id')
            ->paginate(15);
    }

    /**
     * Count how many invitations sit in each state.
     *
     * @return array<string, int>
     */
    private function counts(): array
    {
        $counts = [];

        foreach (AccountInvitationStatus::tabs() as $status) {
            $counts[$status->value] = $this->visibleQuery()->withStatus($status)->count();
        }

        return $counts;
    }

    /**
     * Start a query limited to what this administrator may read.
     *
     * @return Builder<AccountInvitation>
     */
    private function visibleQuery(): Builder
    {
        $query = $this->visibility->query(auth()->user());

        if (trim($this->search) !== '') {
            $search = trim($this->search);

            $query->whereHas('user', fn (Builder $user): Builder => $user
                ->where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%"));
        }

        return $query;
    }

    /**
     * Turn the page of invitations into what the table shows.
     *
     * The row says what an administrator may do and, when an action is off,
     * why it is off. The view stays free of authorization rules.
     *
     * @param  LengthAwarePaginator<int, AccountInvitation>  $invitations
     * @return list<array{id: int, name: string, email: string, inviter: string, created_at: string, expires_at: string, status: AccountInvitationStatus, schools: list<string>, can_resend: bool, can_revoke: bool, reason: string|null}>
     */
    private function rowsFor(LengthAwarePaginator $invitations): array
    {
        $viewer = auth()->user();

        return $invitations->getCollection()
            ->map(function (AccountInvitation $invitation) use ($viewer): array {
                $canResend = Gate::allows('resend', $invitation);
                $canRevoke = Gate::allows('revoke', $invitation);

                return [
                    'id' => $invitation->id,
                    'name' => $invitation->user->name,
                    'email' => $invitation->user->email,
                    'inviter' => $this->inviterName($invitation),
                    'created_at' => $invitation->created_at->format('M j, Y g:ia'),
                    'expires_at' => $invitation->expires_at->format('M j, Y g:ia'),
                    'status' => $invitation->status(),
                    'schools' => $this->visibility->schoolNamesFor($viewer, $invitation->user),
                    'can_resend' => $canResend,
                    'can_revoke' => $canRevoke,
                    'reason' => $canResend && $canRevoke ? null : $this->unavailableReason($invitation),
                ];
            })
            ->all();
    }

    /**
     * Say why the actions are off for this row.
     */
    private function unavailableReason(AccountInvitation $invitation): string
    {
        if ($invitation->user_id === auth()->id()) {
            return 'You cannot resend or revoke your own invitation. Ask another administrator.';
        }

        return match ($invitation->status()) {
            AccountInvitationStatus::Accepted => 'This person already set a password. Change access from their profile instead.',
            AccountInvitationStatus::Revoked => 'This link was stopped. Send a new invitation from the person’s profile.',
            AccountInvitationStatus::Expired => 'This link passed its expiry time. Send a new invitation from the person’s profile.',
            AccountInvitationStatus::Pending => 'You cannot manage invitations for this person.',
        };
    }

    /**
     * Get the name of whoever sent the invitation.
     *
     * An invitation raised by the system itself names nobody.
     */
    private function inviterName(AccountInvitation $invitation): string
    {
        $inviter = $invitation->getRelationValue('invitedBy');

        return $inviter instanceof User ? $inviter->name : 'System';
    }
}
