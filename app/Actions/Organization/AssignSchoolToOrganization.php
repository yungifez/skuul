<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignSchoolToOrganization
{
    public function __construct(private RecordAuditEvent $recordAuditEvent) {}

    /**
     * Assign a campus to an organization without changing school memberships.
     */
    public function assign(School $school, Organization $organization, ?User $actor = null): School
    {
        return DB::transaction(function () use ($school, $organization, $actor): School {
            $school = School::query()->lockForUpdate()->findOrFail($school->id);

            if ($school->organization_id === $organization->id) {
                return $school;
            }

            $previousOrganizationId = $school->organization_id;
            $school->organization()->associate($organization);
            $school->save();

            $this->recordAuditEvent->record(
                AuditAction::SchoolOrganizationAssigned,
                $school,
                [
                    'organization_id' => $organization->id,
                    'previous_organization_id' => $previousOrganizationId,
                ],
                $actor,
                $school,
            );

            return $school;
        }, attempts: 3);
    }
}
