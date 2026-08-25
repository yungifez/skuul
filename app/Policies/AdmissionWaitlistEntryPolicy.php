<?php

namespace App\Policies;

use App\Models\AdmissionWaitlistEntry;
use App\Models\User;

class AdmissionWaitlistEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read admission waitlist');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('read admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage admission waitlist');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('manage admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('manage admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return false;
    }
}
