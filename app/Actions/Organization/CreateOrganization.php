<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrganization
{
    public function __construct(private RecordAuditEvent $recordAuditEvent) {}

    /**
     * @param  array{name: string, code?: string|null, address?: string|null, email?: string|null, phone?: string|null}  $attributes
     */
    public function create(array $attributes, ?User $actor = null): Organization
    {
        return DB::transaction(function () use ($attributes, $actor): Organization {
            $organization = Organization::create([
                ...$attributes,
                'code' => $attributes['code'] ?? Str::upper(Str::random(12)),
            ]);

            $this->recordAuditEvent->record(
                AuditAction::OrganizationCreated,
                $organization,
                ['organization_id' => $organization->id],
                $actor,
            );

            return $organization;
        }, attempts: 3);
    }
}
