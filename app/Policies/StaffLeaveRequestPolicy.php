<?php

namespace App\Policies;

use App\Models\StaffLeaveRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read, ask for, and answer leave.
 *
 * Asking is not answering: a person can ask for their own days without being
 * able to agree to them.
 */
class StaffLeaveRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read staff leave');
    }

    /**
     * Determine whether the user can read one request.
     */
    public function view(User $user, StaffLeaveRequest $request): bool
    {
        if ($request->school_id !== current_school_id()) {
            return false;
        }

        return $user->can('read staff leave') || $request->staffProfile->user_id === $user->id;
    }

    /**
     * Determine whether the user can ask for days away.
     */
    public function create(User $user): bool
    {
        return $user->can('request staff leave');
    }

    /**
     * Determine whether the user can answer the request.
     */
    public function decide(User $user, StaffLeaveRequest $request): bool
    {
        if ($request->school_id !== current_school_id()) {
            return false;
        }

        // Nobody agrees to their own days away.
        return $user->can('approve staff leave') && $request->staffProfile->user_id !== $user->id;
    }

    /**
     * Determine whether the user can change the request.
     */
    public function update(User $user, StaffLeaveRequest $request): bool
    {
        return $this->view($user, $request);
    }
}
