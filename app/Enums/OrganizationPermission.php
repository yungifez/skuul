<?php

namespace App\Enums;

/**
 * Organization membership may narrow these permissions.
 * Campus data access still requires school membership and school-scoped permissions.
 */
enum OrganizationPermission: string
{
    /**
     * See the organization and the list of its campuses.
     */
    case Read = 'read organization';

    /**
     * Change the organization name, code, and contact details.
     */
    case Manage = 'manage organization';

    /**
     * Give and take away organization scope for other people.
     */
    case ManageMembers = 'manage organization members';

    /**
     * Add a campus to the organization and edit its campuses.
     */
    case ManageCampuses = 'manage organization campuses';

    /**
     * Move a student from one campus to another without asking the campuses.
     */
    case MoveStudents = 'move students between campuses';

    /**
     * Read the organization overview totals.
     */
    case ReadReports = 'read organization reports';

    /**
     * Get the permissions a full organization administrator holds.
     *
     * @return list<self>
     */
    public static function all(): array
    {
        return self::cases();
    }

    /**
     * Get the permissions a member can be given one at a time.
     *
     * Read is left out because every member needs it to open the screens.
     *
     * @return list<self>
     */
    public static function delegable(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $permission): bool => $permission !== self::Read,
        ));
    }

    public function label(): string
    {
        return match ($this) {
            self::Read => 'Read organization',
            self::Manage => 'Manage organization settings',
            self::ManageMembers => 'Manage organization members',
            self::ManageCampuses => 'Manage campuses',
            self::MoveStudents => 'Move students between campuses',
            self::ReadReports => 'Read organization reports',
        };
    }

    /**
     * Get the sentence that explains this permission to an administrator.
     */
    public function description(): string
    {
        return match ($this) {
            self::Read => 'See the organization and its campus list.',
            self::Manage => 'Change the organization name, code, and contact details.',
            self::ManageMembers => 'Give and take away organization scope for other people.',
            self::ManageCampuses => 'Add a campus, edit its details, and manage its academic years.',
            self::MoveStudents => 'Move a student to another campus without waiting for that campus to agree.',
            self::ReadReports => 'Read the campus, enrollment, and staff totals.',
        };
    }
}
