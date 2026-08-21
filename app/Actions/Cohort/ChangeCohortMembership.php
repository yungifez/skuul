<?php

namespace App\Actions\Cohort;

use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Put people into a group and take them out again.
 *
 * A place is kept, not deleted, so a school can still see who was in a group
 * last year.
 */
class ChangeCohortMembership
{
    /**
     * Add an enrollment to the group.
     *
     * @throws InvalidValueException when the group and the enrollment are in different schools
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

        $member = CohortMember::firstOrNew([
            'cohort_id'         => $cohort->id,
            'student_record_id' => $enrollment->id,
        ]);

        if ($member->exists && $member->left_on === null) {
            return $member;
        }

        $member->fill([
            'joined_on' => Carbon::parse($joinedOn ?? now()),
            'left_on'   => null,
            'added_by'  => $actor === null ? auth()->id() : $actor->id,
        ])->save();

        return $member;
    }

    /**
     * Add a member of staff or a guardian to the group.
     */
    public function addPerson(
        Cohort $cohort,
        User $person,
        CarbonInterface|string|null $joinedOn = null,
        ?User $actor = null,
    ): CohortMember {
        $member = CohortMember::firstOrNew([
            'cohort_id' => $cohort->id,
            'user_id'   => $person->id,
        ]);

        if ($member->exists && $member->left_on === null) {
            return $member;
        }

        $member->fill([
            'joined_on' => Carbon::parse($joinedOn ?? now()),
            'left_on'   => null,
            'added_by'  => $actor === null ? auth()->id() : $actor->id,
        ])->save();

        return $member;
    }

    /**
     * Take somebody out of the group.
     */
    public function remove(CohortMember $member, CarbonInterface|string|null $leftOn = null): CohortMember
    {
        $member->left_on = Carbon::parse($leftOn ?? now());
        $member->save();

        return $member;
    }
}
