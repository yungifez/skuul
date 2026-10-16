<?php

namespace App\Traits;

use App\Enums\SchoolMembershipStatus;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Require an active membership in the working school for a selected account.
 */
trait ValidatesSchoolMembership
{
    /**
     * Get the rule that the given account works in the working school.
     */
    protected static function memberOfWorkingSchool(): Exists
    {
        return Rule::exists('school_memberships', 'user_id')
            ->where('school_id', current_school_id())
            ->where('status', SchoolMembershipStatus::Active->value);
    }
}
