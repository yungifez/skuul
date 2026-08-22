<?php

namespace App\Enums;

/**
 * The access profiles every school starts with.
 *
 * A role is a permission template that a school can rename, edit, or archive.
 * Business rules must read permissions, not these names. Use this enum only
 * where the application still needs to name a built-in profile, so the names
 * stay in one place while that code moves to permissions.
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

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::PlatformAdmin     => 'Platform Administrator',
            self::OrganizationAdmin => 'Organization Administrator',
            self::Admin             => 'Administrator',
            self::Teacher           => 'Teacher',
            self::Student           => 'Student',
            self::Parent            => 'Parent',
        };
    }
}
