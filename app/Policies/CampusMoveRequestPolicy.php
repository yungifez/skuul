<?php

namespace App\Policies;

use App\Models\CampusMoveRequest;
use App\Models\User;
use App\Services\Authorization\CampusMoveAuthority;

/**
 * Delegate cross-campus access to CampusMoveAuthority.
 * The destination decides, the source cancels, and organization authority permits either.
 */
class CampusMoveRequestPolicy
{
    public function __construct(private CampusMoveAuthority $authority) {}

    /**
     * Determine whether the user can open the campus move screen.
     */
    public function viewAny(User $user): bool
    {
        $school = current_school();

        if ($school === null) {
            return false;
        }

        return $this->authority->canDecideAtCampus($user, $school)
            || $this->authority->canRequest($user, $school);
    }

    /**
     * Determine whether the user can read one request.
     */
    public function view(User $user, CampusMoveRequest $request): bool
    {
        return $this->authority->canDecide($user, $request)
            || $this->authority->canCancel($user, $request);
    }

    /**
     * Determine whether the user can approve or reject the request.
     */
    public function decide(User $user, CampusMoveRequest $request): bool
    {
        return $request->status->isOpen() && $this->authority->canDecide($user, $request);
    }

    /**
     * Determine whether the user can take the request back.
     */
    public function cancel(User $user, CampusMoveRequest $request): bool
    {
        return $request->status->isOpen() && $this->authority->canCancel($user, $request);
    }
}
