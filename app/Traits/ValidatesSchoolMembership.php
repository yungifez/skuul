<?php

namespace App\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * The rule that keeps a named person inside the working school.
 *
 * A form that hands work to somebody must not accept an account from another
 * school. The membership table answers that in one read, so the rule checks
 * it rather than the users table.
 */
trait ValidatesSchoolMembership
{
    /**
     * Get the rule that the given account works in the working school.
     */
    protected function memberOfWorkingSchool(): Exists
    {
        return Rule::exists('school_memberships', 'user_id')
            ->where('school_id', current_school_id());
    }
}
