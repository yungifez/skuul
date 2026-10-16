<?php

namespace App\Policies;

use App\Models\AdmissionWaitlistEntry;
use App\Models\User;

class AdmissionWaitlistEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read admission waitlist');
    }

    public function view(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('read admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    public function create(User $user): bool
    {
        return $user->can('manage admission waitlist');
    }

    public function update(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('manage admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    public function delete(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return $user->can('manage admission waitlist')
            && $admissionWaitlistEntry->school_id === current_school_id();
    }

    public function restore(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return false;
    }

    public function forceDelete(User $user, AdmissionWaitlistEntry $admissionWaitlistEntry): bool
    {
        return false;
    }
}
