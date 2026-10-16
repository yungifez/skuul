<?php

namespace App\Policies;

use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Who may change an enrollment.
 *
 * An enrollment belongs to the campus the learner attends. A learner who moved
 * keeps one enrollment, so the campus they left can still reach it through an
 * old screen or an old link. Only the campus they attend may change it.
 */
class StudentRecordPolicy
{
    /**
     * Determine whether the user can change the enrollment's status or placement.
     */
    public function manage(User $user, StudentRecord $enrollment): Response
    {
        if (!$user->can('update student')) {
            return Response::deny('You cannot change enrollments.');
        }

        return $this->belongsToTheWorkingCampus($enrollment);
    }

    /**
     * Determine whether the user can write the learner's health record.
     */
    public function recordHealth(User $user, StudentRecord $enrollment): Response
    {
        if (!$user->can('update health record')) {
            return Response::deny('You cannot write health records.');
        }

        return $this->belongsToTheWorkingCampus($enrollment);
    }

    /**
     * Allow only the campus the learner attends, and name it for everybody else.
     */
    private function belongsToTheWorkingCampus(StudentRecord $enrollment): Response
    {
        if ($enrollment->school_id === current_school_id()) {
            return Response::allow();
        }

        $school = $enrollment->school()->value('name');

        return Response::deny($school === null
            ? 'This person has no enrollment in the current school.'
            : "This learner now attends {$school}. Only that campus can change their enrollment.");
    }
}
