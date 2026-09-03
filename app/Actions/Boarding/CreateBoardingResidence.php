<?php

namespace App\Actions\Boarding;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\BoardingResidence;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateBoardingResidence
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Create an organization-owned physical residence.
     */
    public function create(
        Organization $organization,
        string $name,
        ?string $notes = null,
        ?User $actor = null,
    ): BoardingResidence {
        return DB::transaction(function () use ($organization, $name, $notes, $actor): BoardingResidence {
            $residence = BoardingResidence::create([
                'organization_id' => $organization->id,
                'name' => $name,
                'notes' => $notes,
            ]);

            $this->auditor->record(
                AuditAction::BoardingResidenceChanged,
                $residence,
                ['change' => 'created', 'organization_id' => $organization->id],
                $actor,
            );

            return $residence;
        });
    }
}
