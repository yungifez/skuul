<?php

namespace App\Livewire;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Actions\Organization\RevokeOrganizationMembership;
use App\Actions\Organization\SetOrganizationMemberPermissions;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationPermission;
use App\Enums\SchoolMembershipStatus;
use App\Exceptions\ApplicationException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Manage who administers one organization, and how much of it.
 *
 * Organization scope is not campus access. Granting it here opens the
 * organization screens only; the campus count in each row is the school
 * access the person already had, and revoking scope leaves it alone.
 */
class OrganizationMembers extends Component
{
    use DispatchesStatusNotifications;

    public Organization $organization;

    /**
     * The email address of the person to give organization scope.
     */
    public string $email = '';

    /**
     * The member whose permissions are being edited.
     */
    public ?int $editingUserId = null;

    /**
     * The member waiting for the administrator to confirm removal.
     *
     * The confirmation is asked for on the page. A browser dialog would stop
     * the rest of the screen from working.
     */
    public ?int $confirmingRemovalUserId = null;

    /**
     * Whether the member being edited holds every organization permission.
     */
    public bool $fullAuthority = true;

    /**
     * The permissions ticked for the member being edited.
     *
     * @var list<string>
     */
    public array $draftPermissions = [];

    public function mount(Organization $organization): void
    {
        Gate::authorize('manageMembers', $organization);

        $this->organization = $organization;
    }

    /**
     * Give organization scope to the person with this email address.
     */
    public function grant(GrantOrganizationMembership $grantOrganizationMembership): void
    {
        Gate::authorize('manageMembers', $this->organization);

        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $this->email)->first();

        if ($user === null) {
            $this->addError('email', 'No account uses that email address.');

            return;
        }

        if ($user->administersOrganization($this->organization)) {
            $this->addError('email', "{$user->name} already administers this organization.");

            return;
        }

        $grantOrganizationMembership->grant($user, $this->organization, auth()->user());

        $this->email = '';
        $this->notify("{$user->name} can now administer {$this->organization->name}.");
    }

    /**
     * Ask the administrator to confirm removal on the page.
     */
    public function confirmRemoval(int $userId): void
    {
        $this->confirmingRemovalUserId = $userId;
        $this->resetValidation();
    }

    /**
     * Drop the pending removal.
     */
    public function cancelRemoval(): void
    {
        $this->confirmingRemovalUserId = null;
    }

    /**
     * Take organization scope away, keeping the person's campus access.
     */
    public function revoke(int $userId, RevokeOrganizationMembership $revokeOrganizationMembership): void
    {
        Gate::authorize('manageMembers', $this->organization);

        $user = User::query()->findOrFail($userId);

        try {
            $revokeOrganizationMembership->revoke($user, $this->organization, auth()->user());
        } catch (ApplicationException $exception) {
            $this->addError('members', $exception->getMessage());

            return;
        }

        $this->stopEditing();
        $this->notify("{$user->name} no longer administers {$this->organization->name}. Their campus access is unchanged.");
    }

    /**
     * Open the permission editor for one member.
     */
    public function edit(int $userId): void
    {
        Gate::authorize('manageMembers', $this->organization);

        $membership = $this->organization->memberships()
            ->active()
            ->where('user_id', $userId)
            ->firstOrFail();

        $this->editingUserId = $userId;
        $this->confirmingRemovalUserId = null;
        $this->fullAuthority = $membership->hasFullAuthority();
        $this->draftPermissions = array_values(array_map(
            fn (OrganizationPermission $permission): string => $permission->value,
            array_filter(
                $membership->grantedPermissions(),
                fn (OrganizationPermission $permission): bool => $permission !== OrganizationPermission::Read,
            ),
        ));
    }

    /**
     * Close the permission editor without saving.
     */
    public function stopEditing(): void
    {
        $this->editingUserId = null;
        $this->confirmingRemovalUserId = null;
        $this->fullAuthority = true;
        $this->draftPermissions = [];
        $this->resetValidation();
    }

    /**
     * Store the permissions delegated to the member being edited.
     */
    public function savePermissions(SetOrganizationMemberPermissions $setOrganizationMemberPermissions): void
    {
        Gate::authorize('manageMembers', $this->organization);

        if ($this->editingUserId === null) {
            return;
        }

        $user = User::query()->findOrFail($this->editingUserId);

        $permissions = $this->fullAuthority ? null : array_values(array_filter(array_map(
            fn (string $value): ?OrganizationPermission => OrganizationPermission::tryFrom($value),
            $this->draftPermissions,
        )));

        try {
            $setOrganizationMemberPermissions->set($user, $this->organization, $permissions, auth()->user());
        } catch (ApplicationException $exception) {
            $this->addError('draftPermissions', $exception->getMessage());

            return;
        }

        $this->stopEditing();
        $this->notify("Permissions updated for {$user->name}.");
    }

    /**
     * Get the permissions an administrator may hand out one at a time.
     *
     * @return list<OrganizationPermission>
     */
    public function getDelegablePermissionsProperty(): array
    {
        return OrganizationPermission::delegable();
    }

    public function render()
    {
        $memberships = $this->memberships();

        return view('livewire.organization-members', [
            'activeMemberships' => $memberships->filter(
                fn (OrganizationMembership $membership): bool => $membership->status === OrganizationMembershipStatus::Active,
            ),
            'pastMemberships' => $memberships->filter(
                fn (OrganizationMembership $membership): bool => $membership->status !== OrganizationMembershipStatus::Active,
            ),
        ]);
    }

    /**
     * Read every membership with the person and their campus count.
     *
     * @return Collection<int, OrganizationMembership>
     */
    private function memberships(): Collection
    {
        return $this->organization->memberships()
            ->with(['user' => fn ($query) => $query->withCount([
                'schoolMemberships as campus_count' => fn (Builder $membership): Builder => $membership->where('status', SchoolMembershipStatus::Active),
            ])])
            ->get()
            ->sortBy(fn (OrganizationMembership $membership): string => $membership->user->name)
            ->values();
    }
}
