<?php

namespace App\Enums;

/**
 * The things a person may do inside an organization.
 *
 * An organization administrator holds all of these by default. A membership
 * can name a smaller set, so one person keeps campus setup while another only
 * reads the overview. None of these permissions open campus records: those
 * still need an active school membership and a school-scoped permission.
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
     * Add a campus to the organization and move one between organizations.
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

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Read           => 'Read organization',
            self::Manage         => 'Manage organization settings',
            self::ManageMembers  => 'Manage organization members',
            self::ManageCampuses => 'Manage campuses',
            self::MoveStudents   => 'Move students between campuses',
            self::ReadReports    => 'Read organization reports',
        };
    }

    /**
     * Get the sentence that explains this permission to an administrator.
     */
    public function description(): string
    {
        return match ($this) {
            self::Read           => 'See the organization and its campus list.',
            self::Manage         => 'Change the organization name, code, and contact details.',
            self::ManageMembers  => 'Give and take away organization scope for other people.',
            self::ManageCampuses => 'Add a campus and move one between organizations.',
            self::MoveStudents   => 'Move a student to another campus without waiting for that campus to agree.',
            self::ReadReports    => 'Read the campus, enrollment, and staff totals.',
        };
    }
}
