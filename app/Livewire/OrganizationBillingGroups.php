<?php

namespace App\Livewire;

use App\Actions\Organization\ManageBillingGroups;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Which campuses of an organization keep one purse.
 *
 * A district whose campuses share a finance office bills a family once. A
 * district whose campuses keep their own accounts does not. Neither is the
 * right answer everywhere, so the organization says which it is.
 */
class OrganizationBillingGroups extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public Organization $organization;

    public string $name = '';

    public function mount(Organization $organization): void
    {
        Gate::authorize('manageDomains', $organization);

        $this->organization = $organization;
    }

    public function startGroup(ManageBillingGroups $manageBillingGroups): void
    {
        Gate::authorize('manageDomains', $this->organization);

        $this->validate(['name' => ['required', 'string', 'max:100']]);

        try {
            $group = $manageBillingGroups->start($this->organization, $this->name, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        $this->reset('name');
        $this->notify("{$group->name} can now hold campuses.");
    }

    /**
     * Put one campus in a group, or let it bill on its own.
     */
    public function placeCampus(int $schoolId, string $groupId, ManageBillingGroups $manageBillingGroups): void
    {
        Gate::authorize('manageDomains', $this->organization);

        $school = $this->organization->schools()->findOrFail($schoolId);

        try {
            $school = $manageBillingGroups->place($this->organization, $school, $groupId === '' ? null : (int) $groupId, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($school->billing_group_id === null
            ? "{$school->name} now bills on its own."
            : "{$school->name} now bills with the rest of its group.");
    }

    public function deleteGroup(int $groupId, ManageBillingGroups $manageBillingGroups): void
    {
        Gate::authorize('manageDomains', $this->organization);

        $group = $this->organization->billingGroups()->findOrFail($groupId);

        try {
            $manageBillingGroups->delete($group, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("{$group->name} was deleted.");
    }

    public function render(): View
    {
        return view('livewire.organization-billing-groups', [
            'groups' => $this->organization->billingGroups()->with('schools:id,name,billing_group_id')->orderBy('name')->get(),
            'campuses' => $this->organization->schools()->orderBy('name')->get(['id', 'name', 'billing_group_id']),
        ]);
    }
}
