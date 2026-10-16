<?php

namespace App\Enums;

enum PlatformPermission: string
{
    case AccessAllSchools = 'access all schools';

    case AccessAllOrganizations = 'access all organizations';

    case ManagePlatform = 'manage platform';
}
