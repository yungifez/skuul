<?php

namespace App\Events;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Raised whenever an account moves between access states.
 *
 * Listeners use this event for notifications and for the account audit trail.
 */
class AccountStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  User  $user  the account that changed
     * @param  AccountStatus  $from  the previous state
     * @param  AccountStatus  $to  the new state
     * @param  User|null  $changedBy  the actor who made the change
     * @param  string|null  $reason  why the change happened
     */
    public function __construct(
        public User $user,
        public AccountStatus $from,
        public AccountStatus $to,
        public ?User $changedBy = null,
        public ?string $reason = null,
    ) {}
}
