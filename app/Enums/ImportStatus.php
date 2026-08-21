<?php

namespace App\Enums;

/**
 * How far along an import is.
 */
enum ImportStatus: string
{
    /**
     * The file is loaded but nothing has been checked.
     */
    case Draft = 'draft';

    /**
     * Every row was checked. The school can see what will happen.
     */
    case Checked = 'checked';

    /**
     * The good rows were written.
     */
    case Applied = 'applied';

    /**
     * The import stopped and wrote nothing.
     */
    case Failed = 'failed';

    /**
     * The school dropped the import.
     */
    case Cancelled = 'cancelled';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Checked => 'Checked',
            self::Applied => 'Applied',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Check if the import can still be written.
     */
    public function canBeApplied(): bool
    {
        return in_array($this, [self::Draft, self::Checked], true);
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
