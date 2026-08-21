<?php

namespace App\Enums;

/**
 * The access states of a user account.
 *
 * The account state is separate from the application state and the
 * enrollment state. A person profile stays intact in every state.
 */
enum AccountStatus: string
{
    /**
     * The account exists but nobody has set a password yet.
     */
    case Invited = 'invited';

    /**
     * The account can sign in and use the application.
     */
    case Active = 'active';

    /**
     * An administrator stopped access. The account can return to active.
     */
    case Suspended = 'suspended';

    /**
     * The account is closed. It stays readable for history.
     */
    case Archived = 'archived';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invited',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Archived => 'Archived',
        };
    }

    /**
     * Check if the account can sign in and use the dashboard.
     */
    public function canAccessApplication(): bool
    {
        return $this === self::Active;
    }

    /**
     * Get the message to show when the account cannot sign in.
     */
    public function accessDeniedMessage(): string
    {
        return match ($this) {
            self::Invited => 'This account is not active yet. Use your invitation link to set a password.',
            self::Suspended => "This account is suspended. If this is an error, contact your school's administrator.",
            self::Archived => "This account is archived. If this is an error, contact your school's administrator.",
            self::Active => '',
        };
    }
}
