<?php

namespace App\Policies;

use App\Models\StudentHealthRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and write health records.
 *
 * Health information is not part of the student profile, so the permission to
 * read a student does not open it.
 */
class StudentHealthRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of health records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read health record');
    }

    /**
     * Determine whether the user can read one health record.
     */
    public function view(User $user, StudentHealthRecord $record): bool
    {
        return $user->can('read health record') && $record->school_id === current_school_id();
    }

    /**
     * Determine whether the user can write a health record.
     */
    public function create(User $user): bool
    {
        return $user->can('update health record');
    }

    /**
     * Determine whether the user can change a health record.
     */
    public function update(User $user, StudentHealthRecord $record): bool
    {
        return $user->can('update health record') && $record->school_id === current_school_id();
    }

    /**
     * Determine whether the user can remove a health record.
     */
    public function delete(User $user, StudentHealthRecord $record): bool
    {
        return $this->update($user, $record);
    }
}
