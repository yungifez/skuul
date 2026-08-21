<?php

namespace App\Actions\School;

use App\Enums\SchoolMembershipStatus;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Give a person access to a school.
 *
 * Calling this again for the same person and school reactivates the existing
 * membership instead of creating a second one, so it is safe to retry.
 */
class GrantSchoolMembership
{
    /**
     * Grant or reactivate access and return the membership.
     */
    public function grant(User $user, School $school, bool $primary = false): SchoolMembership
    {
        return DB::transaction(function () use ($user, $school, $primary): SchoolMembership {
            $membership = $user->schoolMemberships()->firstOrNew(['school_id' => $school->id]);

            $membership->status = SchoolMembershipStatus::Active;
            $membership->ended_at = null;
            $membership->joined_at ??= now();

            $isFirstMembership = !$user->schoolMemberships()
                ->where('school_id', '!=', $school->id)
                ->exists();

            if ($primary || $isFirstMembership) {
                $user->schoolMemberships()->update(['is_primary' => false]);
                $membership->is_primary = true;
            }

            $membership->save();

            return $membership;
        });
    }
}
