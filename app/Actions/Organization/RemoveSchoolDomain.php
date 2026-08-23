<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\SchoolDomain;
use App\Models\User;

/**
 * Give up a web address.
 *
 * The address stops being answered at once. Nothing else is touched: the
 * campus, its people, and its records never belonged to the address.
 */
class RemoveSchoolDomain
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Give up the address.
     */
    public function remove(SchoolDomain $domain, ?User $actor = null): void
    {
        $this->auditor->record(
            AuditAction::SchoolDomainRemoved,
            $domain,
            ['host' => $domain->host, 'organization_id' => $domain->organization_id],
            $actor,
            $domain->school,
        );

        $domain->delete();
    }
}
