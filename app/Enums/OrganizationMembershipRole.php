<?php

namespace App\Enums;

enum OrganizationMembershipRole: string
{
    /**
     * Retained only so historical migrations can be run on a fresh database.
     * Organization authority now comes from Spatie permissions.
     */
    case Admin = 'admin';
}
