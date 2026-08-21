<?php

namespace App\Enums;

/**
 * How a grade item is marked.
 */
enum GradeItemType: string
{
    /**
     * A number out of a maximum, such as 17 out of 20.
     */
    case Numeric = 'numeric';

    /**
     * A named step, such as "Merit" or "Working towards".
     */
    case Scale = 'scale';

    /**
     * Words only. It carries no points and does not change the total.
     */
    case Text = 'text';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Numeric => 'Numeric',
            self::Scale   => 'Scale',
            self::Text    => 'Comment only',
        };
    }

    /**
     * Check if the item adds points to a total.
     */
    public function carriesPoints(): bool
    {
        return $this !== self::Text;
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }
}
