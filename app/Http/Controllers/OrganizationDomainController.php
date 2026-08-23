<?php

namespace App\Http\Controllers;

use App\Actions\Organization\AddSchoolDomain;
use App\Actions\Organization\RemoveSchoolDomain;
use App\Actions\Organization\VerifySchoolDomain;
use App\Http\Requests\StoreSchoolDomainRequest;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The web addresses an organization answers on.
 */
class OrganizationDomainController extends Controller
{
    public function __construct(
        private AddSchoolDomain $addDomain,
        private VerifySchoolDomain $verifyDomain,
        private RemoveSchoolDomain $removeDomain,
    ) {}

    /**
     * Show the addresses, and how to prove a new one.
     */
    public function index(Organization $organization): View
    {
        Gate::authorize('manageDomains', $organization);

        return view('pages.organization.domains', [
            'organization' => $organization,
            'domains' => $organization->domains()->with('school')->orderBy('host')->get(),
            'campuses' => $organization->schools()->orderBy('name')->get(),
        ]);
    }

    /**
     * Claim an address for the organization.
     */
    public function store(StoreSchoolDomainRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('manageDomains', $organization);

        $campusId = $request->integer('school_id');

        $domain = $this->addDomain->add(
            organization: $organization,
            host: $request->string('host')->toString(),
            school: $campusId === 0 ? null : School::findOrFail($campusId),
            isPrimary: $request->boolean('is_primary'),
            actor: $request->user(),
        );

        return back()->with('success', "Add the record below at {$domain->verificationRecord()}, then prove the address.");
    }

    /**
     * Check the record and start answering on the address.
     */
    public function verify(Organization $organization, SchoolDomain $domain): RedirectResponse
    {
        Gate::authorize('manageDomains', $organization);
        abort_unless($domain->organization_id === $organization->id, 404);

        $this->verifyDomain->verify($domain, request()->user());

        return back()->with('success', "$domain->host is proved and now opens this organization.");
    }

    /**
     * Give up an address.
     */
    public function destroy(Organization $organization, SchoolDomain $domain): RedirectResponse
    {
        Gate::authorize('manageDomains', $organization);
        abort_unless($domain->organization_id === $organization->id, 404);

        $host = $domain->host;
        $this->removeDomain->remove($domain, request()->user());

        return back()->with('success', "$host is no longer answered.");
    }
}
