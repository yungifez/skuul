<?php

namespace App\Enums;

enum OrganizationMembershipStatus: string
{
    case Active = 'active';

    case Suspended = 'suspended';

    case Ended = 'ended';

    public function grantsAccess(): bool
    {
        return $this === self::Active;
    }
}
