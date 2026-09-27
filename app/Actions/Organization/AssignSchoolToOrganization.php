<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignSchoolToOrganization
{
    public function __construct(private RecordAuditEvent $recordAuditEvent) {}

    /**
     * Assign a campus to an organization without changing school memberships.
     *
     * The billing group, calendar template and shared residences belong to the
     * organization the campus leaves, so the campus stops using them. It then
     * bills on its own, follows the new organization's default calendar, and
     * keeps its houses as houses of its own.
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
            $school->billing_group_id = null;
            $school->calendar_template_id = null;
            $school->save();

            Dormitory::query()
                ->where('school_id', $school->id)
                ->whereNotNull('boarding_residence_id')
                ->update(['boarding_residence_id' => null]);
            $school->boardingResidences()->detach();

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
