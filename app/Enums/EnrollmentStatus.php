<?php

namespace App\Enums;

/**
 * The states of one student enrollment.
 *
 * The enrollment state is separate from the account state and from the
 * application state. History is kept in enrollment status changes, so no
 * state is ever lost when a student moves on.
 */
enum EnrollmentStatus: string
{
    /**
     * The student attends the school.
     */
    case Active = 'active';

    /**
     * Attendance stopped for a time. The enrollment can return to active.
     */
    case Suspended = 'suspended';

    /**
     * The student left before finishing.
     */
    case Withdrawn = 'withdrawn';

    /**
     * The student moved to another organization.
     */
    case Transferred = 'transferred';

    /**
     * The student finished the program.
     */
    case Graduated = 'graduated';

    /**
     * The enrollment is closed. It stays readable for history.
     */
    case Archived = 'archived';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active      => 'Active',
            self::Suspended   => 'Suspended',
            self::Withdrawn   => 'Withdrawn',
            self::Transferred => 'Transferred',
            self::Graduated   => 'Graduated',
            self::Archived    => 'Archived',
        };
    }

    /**
     * Check if the student attends the school in this state.
     */
    public function isAttending(): bool
    {
        return $this === self::Active;
    }

    /**
     * Check if the enrollment is finished and cannot change placement.
     */
    public function isClosed(): bool
    {
        return in_array($this, [self::Withdrawn, self::Transferred, self::Graduated, self::Archived], true);
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Active      => [self::Suspended, self::Withdrawn, self::Transferred, self::Graduated, self::Archived],
            self::Suspended   => [self::Active, self::Withdrawn, self::Transferred, self::Archived],
            self::Withdrawn   => [self::Active, self::Archived],
            self::Transferred => [self::Archived],
            self::Graduated   => [self::Active, self::Archived],
            self::Archived    => [],
        };
    }

    /**
     * Check if this state can move to the given state.
     */
    public function canMoveTo(self $status): bool
    {
        return in_array($status, $this->allowedNext(), true);
    }
}
