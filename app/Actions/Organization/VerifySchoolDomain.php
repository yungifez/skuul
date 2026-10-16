<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\SchoolDomain;
use App\Models\User;
use App\Services\School\DnsTextRecords;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Require the verification value in DNS before accepting an organization's domain claim.
 */
class VerifySchoolDomain
{
    public function __construct(
        private DnsTextRecords $records,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Check the record and mark the address proved.
     *
     * @throws InvalidValueException when the record is missing or holds another value
     */
    public function verify(SchoolDomain $domain, ?User $actor = null): SchoolDomain
    {
        if ($domain->isVerified()) {
            return $domain;
        }

        $found = $this->records->lookup($domain->verificationRecord());

        if (!in_array($domain->verification_token, array_map('trim', $found), true)) {
            throw new InvalidValueException(
                'The record was not found yet. Add it at '.$domain->verificationRecord().' and try again in a few minutes.'
            );
        }

        $domain->verified_at = now();

        try {
            $domain->save();
        } catch (UniqueConstraintViolationException) {
            $domain->verified_at = null;

            throw new InvalidValueException("Another organization proved {$domain->host} first.");
        }

        $this->auditor->record(
            AuditAction::SchoolDomainVerified,
            $domain,
            ['host' => $domain->host],
            $actor,
            $domain->school,
        );

        return $domain;
    }
}
