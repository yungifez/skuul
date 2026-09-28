<?php

namespace App\Actions\School;

use App\Actions\Curriculum\AssignTeacher;
use App\Enums\SchoolMembershipStatus;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Stop a person's access to one school.
 *
 * The membership record stays so the history remains readable. The person, and
 * their records in that school, are not deleted. The subjects they still teach
 * there end, so each class shows it needs a teacher.
 */
class EndSchoolMembership
{
    public function __construct(private AssignTeacher $teaching) {}

    /**
     * End the membership and return it, or null when there was none.
     */
    public function end(User $user, School $school): ?SchoolMembership
    {
        return DB::transaction(function () use ($user, $school): ?SchoolMembership {
            $membership = $user->schoolMemberships()
                ->where('school_id', $school->id)
                ->first();

            if ($membership === null || $membership->status === SchoolMembershipStatus::Ended) {
                return $membership;
            }

            $membership->status = SchoolMembershipStatus::Ended;
            $membership->ended_at = now();
            $membership->is_primary = false;
            $membership->save();

            $user->schoolMemberships()->where('school_id', $school->id)->update(['is_primary' => false]);

            TeachingAssignment::query()
                ->where('school_id', $school->id)
                ->forTeacher($user)
                ->where(fn ($running) => $running->whereNull('ends_on')->orWhereDate('ends_on', '>', today()))
                ->get()
                ->each(fn (TeachingAssignment $assignment) => $this->teaching->end($assignment, today()));

            $this->promoteAnotherPrimary($user);

            return $membership;
        });
    }

    /**
     * Make sure the person still has a school for organization-level work.
     */
    private function promoteAnotherPrimary(User $user): void
    {
        $hasPrimary = $user->schoolMemberships()->active()->primary()->exists();

        if ($hasPrimary) {
            return;
        }

        $next = $user->schoolMemberships()->active()->orderBy('id')->first();

        $next?->update(['is_primary' => true]);
    }

    /**
     * End the membership, or fail when the person is not a member.
     */
    public function endOrFail(User $user, School $school): SchoolMembership
    {
        $membership = $this->end($user, $school);

        if ($membership === null) {
            throw new RuntimeException("{$user->name} is not a member of {$school->name}.");
        }

        return $membership;
    }
}
