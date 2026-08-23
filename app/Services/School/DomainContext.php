<?php

namespace App\Services\School;

use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use Illuminate\Http\Request;

/**
 * Which organization, and perhaps which campus, this address belongs to.
 *
 * A school on its own address should open on its own campus without anybody
 * choosing one from a list. The address is a hint about what the visitor
 * meant, nothing more: membership still decides what they may see, and an
 * address nobody has proved ownership of is ignored.
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
     */
    public function school(): ?School
    {
        return $this->domain?->school;
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
