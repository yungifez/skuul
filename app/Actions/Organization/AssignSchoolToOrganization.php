<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Enrollment\RequestCampusMove;
use App\Enums\AuditAction;
use App\Enums\CampusMoveStatus;
use App\Models\CampusMoveRequest;
use App\Models\Dormitory;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolDomain;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AssignSchoolToOrganization
{
    public function __construct(
        private RecordAuditEvent $recordAuditEvent,
        private RequestCampusMove $campusMoves,
    ) {}

    /**
     * Assign a campus to an organization without changing school memberships.
     *
     * The billing group, calendar template and shared residences belong to the
     * organization the campus leaves, so the campus stops using them. It then
     * bills on its own, follows the new organization's default calendar, and
     * keeps its houses as houses of its own. An address the old organization
     * proved no longer opens it. A move still waiting between it and a campus
     * of another organization can never be made, so it is cancelled.
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
            SchoolDomain::query()
                ->where('school_id', $school->id)
                ->where('organization_id', '!=', $organization->id)
                ->update(['school_id' => null]);

            CampusMoveRequest::query()
                ->where('status', CampusMoveStatus::Requested)
                ->where(fn (Builder $requests): Builder => $requests->where('from_school_id', $school->id)->orWhere('to_school_id', $school->id))
                ->with(['fromSchool:id,organization_id', 'toSchool:id,organization_id'])
                ->get()
                ->reject(fn (CampusMoveRequest $request): bool => $request->fromSchool?->organization_id === $request->toSchool?->organization_id)
                ->each(fn (CampusMoveRequest $request) => $this->campusMoves->cancel($request, $actor, "{$school->name} left the organization."));

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
