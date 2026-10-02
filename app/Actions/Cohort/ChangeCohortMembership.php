<?php

namespace App\Actions\Cohort;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Put people into a group and take them out again.
 *
 * A place is kept, not deleted, so a school can still see who was in a group
 * last year.
 */
class ChangeCohortMembership
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Add an enrollment to the group.
     *
     * A suspended learner still attends and may join. A learner who left,
     * moved on or graduated may not.
     *
     * @throws InvalidValueException when the enrollment is in another school or closed, the group is closed or the day has not come
     */
    public function addStudent(
        Cohort $cohort,
        StudentRecord $enrollment,
        CarbonInterface|string|null $joinedOn = null,
        ?User $actor = null,
    ): CohortMember {
        if ($cohort->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('A student can only join a group in their own school.');
        }

        if (!in_array($enrollment->status, EnrollmentStatus::enrolled(), true)) {
            throw new InvalidValueException("This learner no longer attends the school ({$enrollment->status->label()}).");
        }

        return $this->add($cohort, ['student_record_id' => $enrollment->id], $joinedOn, $actor);
    }

    /**
     * Add a member of staff or a guardian to the group.
     *
     * @throws InvalidValueException when the group is closed or the day has not come
     */
    public function addPerson(
        Cohort $cohort,
        User $person,
        CarbonInterface|string|null $joinedOn = null,
        ?User $actor = null,
    ): CohortMember {
        return $this->add($cohort, ['user_id' => $person->id], $joinedOn, $actor);
    }

    /**
     * Take somebody out of the group.
     *
     * @throws InvalidValueException when the person already left
     */
    public function remove(CohortMember $member, CarbonInterface|string|null $leftOn = null, ?User $actor = null): CohortMember
    {
        return DB::transaction(function () use ($member, $leftOn, $actor): CohortMember {
            $member = CohortMember::query()->lockForUpdate()->findOrFail($member->getKey());

            if ($member->left_on !== null) {
                throw new InvalidValueException('This person already left the group.');
            }

            $leftOn = Carbon::parse($leftOn ?? now())->startOfDay();
            $member->left_on = $member->joined_on !== null && $member->joined_on->greaterThan($leftOn) ? $member->joined_on : $leftOn;
            $member->save();

            $this->audit->record(AuditAction::CohortMembershipChanged, $member->cohort, [
                'member_id' => $member->id,
                'student_record_id' => $member->student_record_id,
                'user_id' => $member->user_id,
                'left_on' => $member->left_on->toDateString(),
            ], $actor);

            return $member;
        });
    }

    /**
     * Take the learner out of every group they are still in at one campus.
     *
     * @return int the number of groups left
     */
    public function leaveSchool(StudentRecord $enrollment, int $schoolId, ?CarbonInterface $leftOn = null, ?User $actor = null): int
    {
        $places = CohortMember::query()
            ->where('student_record_id', $enrollment->id)
            ->whereNull('left_on')
            ->whereHas('cohort', fn ($cohorts) => $cohorts->where('school_id', $schoolId))
            ->get();

        foreach ($places as $place) {
            $this->remove($place, $leftOn, $actor);
        }

        return $places->count();
    }

    /**
     * Give the person a place, or open the place they held before again.
     *
     * @param  array{student_record_id: int}|array{user_id: int}  $holder
     *
     * @throws InvalidValueException
     */
    private function add(Cohort $cohort, array $holder, CarbonInterface|string|null $joinedOn, ?User $actor): CohortMember
    {
        $joinedOn = Carbon::parse($joinedOn ?? now())->startOfDay();

        if ($joinedOn->isAfter(school_today())) {
            throw new InvalidValueException('Nobody can join a group on a day that has not come yet.');
        }

        try {
            return DB::transaction(function () use ($cohort, $holder, $joinedOn, $actor): CohortMember {
                $cohort = Cohort::query()->lockForUpdate()->findOrFail($cohort->getKey());

                if (!$cohort->is_active) {
                    throw new InvalidValueException("{$cohort->name} is closed. Open it again before anybody joins.");
                }

                $member = CohortMember::firstOrNew(['cohort_id' => $cohort->id, ...$holder]);

                if ($member->exists && $member->left_on === null) {
                    return $member;
                }

                $member->fill([
                    'joined_on' => $joinedOn,
                    'left_on' => null,
                    'added_by' => $actor === null ? auth()->id() : $actor->id,
                ])->save();

                $this->audit->record(AuditAction::CohortMembershipChanged, $cohort, [
                    'member_id' => $member->id,
                    ...$holder,
                    'joined_on' => $joinedOn->toDateString(),
                ], $actor);

                return $member;
            });
        } catch (UniqueConstraintViolationException) {
            return CohortMember::query()->where('cohort_id', $cohort->id)->where($holder)->sole();
        }
    }
}
