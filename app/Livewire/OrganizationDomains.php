<?php

namespace App\Livewire;

use App\Actions\Organization\AddSchoolDomain;
use App\Actions\Organization\RemoveSchoolDomain;
use App\Actions\Organization\VerifySchoolDomain;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\Organization;
use App\Models\SchoolDomain;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The web addresses an organization answers on.
 *
 * An address only says which campus a visitor meant. It is ignored until the
 * organization proves it owns it, and membership still decides what anybody
 * may see.
 */
class OrganizationDomains extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public Organization $organization;

    public string $host = '';

    public string $schoolId = '';

    public bool $isPrimary = false;

    public function mount(Organization $organization): void
    {
        Gate::authorize('manageDomains', $organization);

        $this->organization = $organization;
    }

    public function claim(AddSchoolDomain $addSchoolDomain): void
    {
        Gate::authorize('manageDomains', $this->organization);

        $this->validate([
            'host' => ['required', 'string', 'max:253'],
            'schoolId' => ['nullable', 'integer'],
        ], [], ['schoolId' => 'campus']);

        $school = $this->schoolId === '' ? null : $this->organization->schools()->findOrFail((int) $this->schoolId);

        try {
            $domain = $addSchoolDomain->add($this->organization, $this->host, $school, $this->isPrimary, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('host', $exception->getMessage());

            return;
        }

        $this->reset('host', 'schoolId', 'isPrimary');
        $this->notify("Add the record shown under {$domain->host}, then prove it.");
    }

    public function verify(int $domainId, VerifySchoolDomain $verifySchoolDomain): void
    {
        $domain = $this->domain($domainId);

        try {
            $verifySchoolDomain->verify($domain, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify("{$domain->host} is proved and now opens this organization.");
    }

    public function giveUp(int $domainId, RemoveSchoolDomain $removeSchoolDomain): void
    {
        $domain = $this->domain($domainId);
        $removeSchoolDomain->remove($domain, auth()->user());

        $this->notify("{$domain->host} is no longer answered.");
    }

    public function render(): View
    {
        return view('livewire.organization-domains', [
            'domains' => $this->organization->domains()->with('school:id,name,organization_id')->orderByDesc('is_primary')->orderBy('host')->get(),
            'campuses' => $this->organization->schools()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function domain(int $domainId): SchoolDomain
    {
        Gate::authorize('manageDomains', $this->organization);

        return $this->organization->domains()->findOrFail($domainId);
    }
}
