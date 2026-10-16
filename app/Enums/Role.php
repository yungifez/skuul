<?php

namespace App\Enums;

/**
 * Built-in role names for seeding and compatibility checks.
 * Enforce operational access through permissions.
 */
enum Role: string
{
    /**
     * Administers the platform through globally-scoped permissions.
     */
    case PlatformAdmin = 'platform-admin';

    /**
     * Manages an organization, subject to an active organization membership.
     */
    case OrganizationAdmin = 'organization-admin';

    /**
     * Runs one school.
     */
    case Admin = 'admin';

    /**
     * Teaches subjects and records marks.
     */
    case Teacher = 'teacher';

    /**
     * Attends the school.
     */
    case Student = 'student';

    /**
     * Follows the record of a student.
     */
    case Parent = 'parent';

    /**
     * Check whether this role is assigned in the system scope rather than a school.
     */
    public function isSystemScoped(): bool
    {
        return match ($this) {
            self::PlatformAdmin, self::OrganizationAdmin => true,
            default => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PlatformAdmin => 'Platform Administrator',
            self::OrganizationAdmin => 'Organization Administrator',
            self::Admin => 'Administrator',
            self::Teacher => 'Teacher',
            self::Student => 'Student',
            self::Parent => 'Parent',
        };
    }
}
