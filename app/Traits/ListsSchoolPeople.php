<?php

namespace App\Traits;

use App\Enums\EnrollmentStatus;
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
     * Get every learner the working school has enrolled, past ones included.
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
     * Get the learners who still attend the working school, suspended ones included.
     *
     * @return Collection<int, StudentRecord>
     */
    protected function attendingLearners(): Collection
    {
        return StudentRecord::query()
            ->inSchool()
            ->enrolled()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get(['id', 'user_id', 'admission_number']);
    }

    /**
     * Get the people who work in the working school.
     *
     * A learner never handles a case or runs a plan, so the list leaves out
     * anybody enrolled in this school, and anybody still attending another.
     * A learner who moved keeps their membership here.
     *
     * @return Collection<int, User>
     */
    protected function schoolStaff(): Collection
    {
        return User::query()
            ->ofSchool()
            ->whereDoesntHave('studentRecords', function (Builder $query): void {
                $query->where('school_id', current_school_id())
                    ->orWhereIn('status', EnrollmentStatus::enrolled());
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
