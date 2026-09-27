<?php

namespace App\Enums;

/**
 * How far a planned topic was taught to one class.
 *
 * A topic with no coverage record has not been taught yet.
 */
enum TopicCoverageStatus: string
{
    case Covered = 'covered';
    case Partial = 'partial';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Covered => 'Covered',
            self::Partial => 'Partly covered',
            self::Skipped => 'Skipped',
        };
    }

    /**
     * Check if the topic needs no more teaching time.
     */
    public function isSettled(): bool
    {
        return $this === self::Covered || $this === self::Skipped;
    }
}
