<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\SchoolDomain;
use App\Models\User;
use App\Services\School\DnsTextRecords;

/**
 * Prove that an organization owns a web address.
 *
 * Only the owner of an address can write a record inside it. The application
 * hands out a value, looks for it, and follows the address only once it is
 * there. Until then the address is ignored, so pointing a name at this
 * application does not make it answer as somebody else's school.
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
        $domain->save();

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
