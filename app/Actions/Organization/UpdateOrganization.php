<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateOrganization
{
    public function __construct(private RecordAuditEvent $recordAuditEvent)
    {
    }

    /**
     * @param array{name: string, code?: string|null, address?: string|null, email?: string|null, phone?: string|null} $attributes
     */
    public function update(Organization $organization, array $attributes, ?User $actor = null): Organization
    {
        return DB::transaction(function () use ($organization, $attributes, $actor): Organization {
            $organization->update($attributes);

            $this->recordAuditEvent->record(
                AuditAction::OrganizationUpdated,
                $organization,
                ['organization_id' => $organization->id, 'changed' => array_keys($attributes)],
                $actor,
            );

            return $organization;
        }, attempts: 3);
    }
}
