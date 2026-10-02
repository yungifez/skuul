<?php

namespace App\Enums;

/**
 * The states of a notice.
 */
enum NoticeStatus: string
{
    /**
     * Being written. Nobody can see it yet.
     */
    case Draft = 'draft';

    /**
     * Waiting for its day to arrive.
     */
    case Scheduled = 'scheduled';

    /**
     * On the board for its audience to read.
     */
    case Published = 'published';

    /**
     * Its day has passed. It stays readable in history.
     */
    case Expired = 'expired';

    /**
     * Replaced by a later, published correction. It stays in the audit trail.
     */
    case Superseded = 'superseded';

    /**
     * Taken off the board on purpose.
     */
    case Archived = 'archived';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Scheduled => 'Scheduled',
            self::Published => 'Published',
            self::Expired => 'Expired',
            self::Superseded => 'Superseded',
            self::Archived => 'Archived',
        };
    }

    /**
     * Check if the audience can read the notice in this state.
     */
    public function isVisible(): bool
    {
        return $this === self::Published;
    }

    /**
     * Get the states this state can move to.
     *
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft => [self::Scheduled, self::Published, self::Archived],
            self::Scheduled => [self::Published, self::Draft, self::Archived],
            self::Published => [self::Expired, self::Superseded, self::Archived],
            self::Expired => [self::Archived],
            self::Superseded => [],
            self::Archived => [],
        };
    }

    /**
     * Check if this state can move to the given state.
     */
    public function canMoveTo(self $status): bool
    {
        return in_array($status, $this->allowedNext(), true);
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $status): string => $status->value, self::cases());
    }
}
