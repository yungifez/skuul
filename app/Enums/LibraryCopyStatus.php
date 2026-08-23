<?php

namespace App\Enums;

/**
 * What has become of one copy on the shelf.
 *
 * Being out on loan is not listed here. That is answered by the loans
 * themselves, so the shelf and the loan record can never disagree.
 */
enum LibraryCopyStatus: string
{
    case OnShelf = 'on_shelf';

    case InRepair = 'in_repair';

    case Lost = 'lost';

    case Withdrawn = 'withdrawn';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::OnShelf => 'On the shelf',
            self::InRepair => 'Being repaired',
            self::Lost => 'Lost',
            self::Withdrawn => 'Taken out of the library',
        };
    }

    /**
     * Check whether the copy can go out to somebody.
     */
    public function canBeLent(): bool
    {
        return $this === self::OnShelf;
    }

    /**
     * Check whether the library still counts the copy as its own.
     */
    public function isHeld(): bool
    {
        return in_array($this, [self::OnShelf, self::InRepair], true);
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
