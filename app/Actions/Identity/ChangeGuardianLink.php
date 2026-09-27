<?php

namespace App\Actions\Identity;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\ParentRecord;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Link a guardian to a learner of the working school, or take the link away.
 *
 * A link lets the guardian read the learner's records in the portal, so each
 * change is audited. One guardian can have children at several schools, and a
 * school only changes the links to its own learners.
 */
class ChangeGuardianLink
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * @throws InvalidValueException when the learner does not attend the working school
     */
    public function link(User $guardian, User $learner, User $actor): void
    {
        DB::transaction(function () use ($guardian, $learner, $actor): void {
            $parentRecord = $this->lockedParentRecord($guardian);
            $this->refuseALearnerOfAnotherSchool($learner);

            if ($parentRecord->students()->whereKey($learner->getKey())->exists()) {
                return;
            }

            $parentRecord->students()->attach($learner->getKey());
            $this->audit->record(AuditAction::GuardianLinkChanged, $guardian, ['learner_id' => $learner->getKey(), 'linked' => true], $actor);
        });
    }

    /**
     * @throws InvalidValueException when the learner does not attend the working school
     */
    public function unlink(User $guardian, User $learner, User $actor): void
    {
        DB::transaction(function () use ($guardian, $learner, $actor): void {
            $parentRecord = $this->lockedParentRecord($guardian);
            $this->refuseALearnerOfAnotherSchool($learner);

            if ($parentRecord->students()->detach($learner->getKey()) === 0) {
                return;
            }

            $this->audit->record(AuditAction::GuardianLinkChanged, $guardian, ['learner_id' => $learner->getKey(), 'linked' => false], $actor);
        });
    }

    private function lockedParentRecord(User $guardian): ParentRecord
    {
        ParentRecord::query()->firstOrCreate(['user_id' => $guardian->getKey()]);

        return ParentRecord::query()->where('user_id', $guardian->getKey())->lockForUpdate()->firstOrFail();
    }

    /**
     * @throws InvalidValueException when the learner has no enrollment here
     */
    private function refuseALearnerOfAnotherSchool(User $learner): void
    {
        if (!StudentRecord::query()->inSchool()->where('user_id', $learner->getKey())->exists()) {
            throw new InvalidValueException('This learner does not attend this school.');
        }
    }
}
