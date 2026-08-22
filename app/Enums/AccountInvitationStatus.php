<?php

namespace App\Enums;

/**
 * The state of one invitation link.
 *
 * The state comes from the invitation timestamps. Acceptance wins over
 * revocation, and revocation wins over expiry, so a link shows one state only.
 */
enum AccountInvitationStatus: string
{
    /**
     * The person can still open the link and set a password.
     */
    case Pending = 'pending';

    /**
     * The person used the link and the account is active.
     */
    case Accepted = 'accepted';

    /**
     * The link passed its expiry time without being used.
     */
    case Expired = 'expired';

    /**
     * An administrator stopped the link before the person used it.
     */
    case Revoked = 'revoked';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Expired => 'Expired',
            self::Revoked => 'Revoked',
        };
    }

    /**
     * Get the sentence that explains this state to an administrator.
     */
    public function description(): string
    {
        return match ($this) {
            self::Pending => 'Waiting for the person to set a password.',
            self::Accepted => 'The person set a password and can sign in.',
            self::Expired => 'The link passed its expiry time and no longer works.',
            self::Revoked => 'An administrator stopped the link.',
        };
    }

    /**
     * Get the april badge variant that shows this state.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'default',
            self::Accepted => 'secondary',
            self::Expired, self::Revoked => 'outline',
        };
    }

    /**
     * Get the lucide icon that shows this state.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'mail',
            self::Accepted => 'circle-check',
            self::Expired => 'clock',
            self::Revoked => 'ban',
        };
    }

    /**
     * Get the states shown as tabs, in reading order.
     *
     * @return list<self>
     */
    public static function tabs(): array
    {
        return [self::Pending, self::Accepted, self::Expired, self::Revoked];
    }
}
