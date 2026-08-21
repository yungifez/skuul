<?php

namespace App\Policies;

use App\Models\DataSharingRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may ask for another school's records, answer the ask, and hand them over.
 *
 * The three are separate permissions on purpose. The school that asks never
 * approves its own request, and approving is not the same as handing the
 * records over.
 */
class DataSharingRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('request data sharing') || $user->can('approve data sharing');
    }

    /**
     * Determine whether the user can read one request.
     *
     * Both schools read it: the one that asked and the one that decides.
     */
    public function view(User $user, DataSharingRequest $request): bool
    {
        $school = current_school_id();

        if ($school !== $request->requesting_school_id && $school !== $request->holding_school_id) {
            return false;
        }

        return $user->can('request data sharing') || $user->can('approve data sharing');
    }

    /**
     * Determine whether the user can ask another school.
     */
    public function create(User $user): bool
    {
        return $user->can('request data sharing');
    }

    /**
     * Determine whether the user can answer the request.
     *
     * Only the school that holds the records decides.
     */
    public function decide(User $user, DataSharingRequest $request): bool
    {
        return $user->can('approve data sharing') && current_school_id() === $request->holding_school_id;
    }

    /**
     * Determine whether the user can hand the records over.
     */
    public function fulfil(User $user, DataSharingRequest $request): bool
    {
        return $user->can('fulfil data sharing') && current_school_id() === $request->holding_school_id;
    }
}
