<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Claim a web address for an organization.
 *
 * The address is written down as a claim. It does nothing at all until the
 * organization proves it owns it, because otherwise anybody could point a name
 * at this application and have it answer as somebody else's school.
 */
class AddSchoolDomain
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Claim the address.
     *
     * @throws InvalidValueException when the address is malformed, already
     *                               claimed, or names a campus of another organization
     */
    public function add(
        Organization $organization,
        string $host,
        ?School $school = null,
        bool $isPrimary = false,
        ?User $actor = null,
    ): SchoolDomain {
        $host = SchoolDomain::tidy($host);

        if (!$this->looksLikeAHost($host)) {
            throw new InvalidValueException("[$host] is not a web address this application can answer on.");
        }

        if (SchoolDomain::where('host', $host)->exists()) {
            throw new InvalidValueException("[$host] is already claimed.");
        }

        if ($school !== null && $school->organization_id !== $organization->id) {
            throw new InvalidValueException('That campus belongs to another organization.');
        }

        $domain = SchoolDomain::create([
            'organization_id' => $organization->id,
            'school_id' => $school?->id,
            'host' => $host,
            'is_primary' => $isPrimary,
            'verification_token' => Str::lower(Str::random(32)),
            'created_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::SchoolDomainAdded,
            $domain,
            ['host' => $host, 'organization_id' => $organization->id, 'school_id' => $school?->id],
            $actor,
            $school,
        );

        return $domain;
    }

    /**
     * Check that the address is a name and not a path, a port, or a sentence.
     */
    private function looksLikeAHost(string $host): bool
    {
        return $host !== ''
            && strlen($host) <= 253
            && preg_match('/^(?!-)[a-z0-9-]{1,63}(?<!-)(\.(?!-)[a-z0-9-]{1,63}(?<!-))+$/', $host) === 1;
    }
}
