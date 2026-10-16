<?php

namespace App\Services\School;

use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use Illuminate\Http\Request;

/**
 * Resolve verified organization and campus domains.
 * The domain selects context; membership still controls access.
 */
class DomainContext
{
    private ?SchoolDomain $domain = null;

    private bool $resolved = false;

    /**
     * Work out which address this request came in on.
     */
    public function resolveFor(Request $request): ?SchoolDomain
    {
        $this->domain = SchoolDomain::forHost($request->getHost());
        $this->resolved = true;

        return $this->domain;
    }

    /**
     * Get the address this request came in on, if it is a known one.
     */
    public function domain(): ?SchoolDomain
    {
        return $this->domain;
    }

    /**
     * Check whether this request came in on a known address.
     */
    public function has(): bool
    {
        return $this->domain !== null;
    }

    /**
     * Check whether the address was looked up for this request.
     */
    public function isResolved(): bool
    {
        return $this->resolved;
    }

    /**
     * Get the organization this address belongs to.
     */
    public function organization(): ?Organization
    {
        return $this->domain?->organization;
    }

    /**
     * Get the campus this address opens, when it names one.
     *
     * A campus that has since joined another organization is not opened by
     * an address its old organization proved.
     */
    public function school(): ?School
    {
        $school = $this->domain?->school;

        if ($school === null || $school->organization_id !== $this->domain->organization_id) {
            return null;
        }

        return $school;
    }

    /**
     * Set the address by hand, which the tests and the console need.
     */
    public function set(?SchoolDomain $domain): void
    {
        $this->domain = $domain;
        $this->resolved = true;
    }

    /**
     * Forget the address of this request.
     */
    public function forget(): void
    {
        $this->domain = null;
        $this->resolved = false;
    }
}
