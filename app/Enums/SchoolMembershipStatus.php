<?php

namespace App\Enums;

/**
 * The states of a person's access to one school.
 *
 * Membership is separate from the account state and from student enrollment.
 * Ending a membership removes access but keeps the person and their records.
 */
enum SchoolMembershipStatus: string
{
    /**
     * The person can work in this school.
     */
    case Active = 'active';

    /**
     * An administrator stopped access to this school only.
     */
    case Suspended = 'suspended';

    /**
     * The person left this school. The record stays for history.
     */
    case Ended = 'ended';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Active',
            self::Suspended => 'Suspended',
            self::Ended     => 'Ended',
        };
    }

    /**
     * Check if the membership grants access to the school.
     */
    public function grantsAccess(): bool
    {
        return $this === self::Active;
    }
}
