<?php

namespace App\Policies;

use App\Models\PortalRequest;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read and answer what a family asked for.
 *
 * The family reads its own requests through the portal. The school reads them
 * with a permission, and only the school answers.
 */
class PortalRequestPolicy
{
    use HandlesAuthorization;

    public function __construct(private PortalAccess $access)
    {
    }

    /**
     * Determine whether the user can see a list of requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read portal request') || $this->access->isOpen();
    }

    /**
     * Determine whether the user can read one request.
     */
    public function view(User $user, PortalRequest $request): bool
    {
        if ($request->requested_by === $user->id) {
            return true;
        }

        if ($this->access->canRead($user, $request->studentRecord)) {
            return true;
        }

        return $user->can('read portal request') && $request->school_id === current_school_id();
    }

    /**
     * Determine whether the user can answer the request.
     *
     * A family never answers its own request, whatever else they hold.
     */
    public function answer(User $user, PortalRequest $request): bool
    {
        return $user->can('answer portal request')
            && $request->school_id === current_school_id()
            && $request->requested_by !== $user->id;
    }
}
