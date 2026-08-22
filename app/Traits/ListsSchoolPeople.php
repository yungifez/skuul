<?php

namespace App\Traits;

use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * The two lists a screen needs when it names a person.
 *
 * A screen that hands work to somebody, or records something against a child,
 * asks the same two questions: who learns here, and who works here. Both
 * answers stay inside the working school.
 */
trait ListsSchoolPeople
{
    /**
     * Get the learners enrolled in the working school.
     *
     * @return Collection<int, StudentRecord>
     */
    protected function schoolLearners(): Collection
    {
        return StudentRecord::query()
            ->inSchool()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get(['id', 'user_id', 'admission_number']);
    }

    /**
     * Get the people who work in the working school.
     *
     * A learner never handles a case or runs a plan, so the list leaves out
     * anybody enrolled in this school.
     *
     * @return Collection<int, User>
     */
    protected function schoolStaff(): Collection
    {
        return User::query()
            ->whereHas('schoolMemberships', function (Builder $query): void {
                $query->where('school_id', current_school_id());
            })
            ->whereDoesntHave('studentRecords', function (Builder $query): void {
                $query->where('school_id', current_school_id());
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
