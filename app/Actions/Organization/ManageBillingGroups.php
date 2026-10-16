<?php

namespace App\Actions\Organization;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\BillingGroup;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Say which campuses of an organization keep one purse.
 *
 * Nothing already in the books moves. A balance follows a learner only when
 * the learner moves to another campus of the same group.
 */
class ManageBillingGroups
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Start an empty group.
     *
     * @throws InvalidValueException when the organization already has a group with that name
     */
    public function start(Organization $organization, string $name, ?User $actor = null): BillingGroup
    {
        $name = trim($name);

        return DB::transaction(function () use ($organization, $name, $actor): BillingGroup {
            Organization::query()->lockForUpdate()->findOrFail($organization->id);

            if ($organization->billingGroups()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->exists()) {
                throw new InvalidValueException('This organization already has a group with that name.');
            }

            $group = BillingGroup::create(['organization_id' => $organization->id, 'name' => $name]);
            $this->auditor->record(AuditAction::BillingGroupChanged, $group, ['change' => 'started'], $actor);

            return $group;
        });
    }

    /**
     * Put a campus in a group, or let it bill on its own when the group is null.
     *
     * @throws InvalidValueException when the campus or group belongs to another organization, or the group was deleted
     */
    public function place(Organization $organization, School $school, ?int $groupId, ?User $actor = null): School
    {
        return DB::transaction(function () use ($organization, $school, $groupId, $actor): School {
            $school = School::query()->lockForUpdate()->findOrFail($school->id);

            if ($school->organization_id !== $organization->id) {
                throw new InvalidValueException('That campus belongs to another organization.');
            }

            if ($groupId !== null) {
                // The lock waits for a delete of the same group to finish, so
                // a campus is never placed in a group that is gone.
                $group = BillingGroup::query()->lockForUpdate()->find($groupId);

                if ($group === null || $group->organization_id !== $organization->id) {
                    throw new InvalidValueException('That group no longer exists. Choose another.');
                }
            }

            if ($school->billing_group_id === $groupId) {
                return $school;
            }

            $previousGroupId = $school->billing_group_id;
            $school->billing_group_id = $groupId;
            $school->save();

            $this->auditor->record(
                AuditAction::BillingGroupChanged,
                $school,
                ['change' => 'campus_placed', 'billing_group_id' => $groupId, 'previous_billing_group_id' => $previousGroupId],
                $actor,
                $school,
            );

            return $school;
        });
    }

    /**
     * Delete a group no campus is in.
     *
     * @throws InvalidValueException when a campus is still in the group
     */
    public function delete(BillingGroup $group, ?User $actor = null): void
    {
        DB::transaction(function () use ($group, $actor): void {
            $group = BillingGroup::query()->lockForUpdate()->findOrFail($group->id);

            if ($group->schools()->lockForUpdate()->exists()) {
                throw new InvalidValueException("Take every campus out of {$group->name} first.");
            }

            $group->delete();
            $this->auditor->record(AuditAction::BillingGroupChanged, $group, ['change' => 'deleted', 'name' => $group->name], $actor);
        });
    }
}
