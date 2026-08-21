<?php

namespace App\Enums;

/**
 * What happened to one row of an import.
 */
enum ImportRowState: string
{
    /**
     * Read from the file, not checked yet.
     */
    case Pending = 'pending';

    /**
     * Checked and good.
     */
    case Valid = 'valid';

    /**
     * Checked and wrong. The row says why.
     */
    case Invalid = 'invalid';

    /**
     * Written to the database.
     */
    case Applied = 'applied';

    /**
     * Left alone, because the record already says this.
     */
    case Skipped = 'skipped';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Valid   => 'Ready',
            self::Invalid => 'Has errors',
            self::Applied => 'Written',
            self::Skipped => 'Skipped',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $state): string => $state->value, self::cases());
    }
}
