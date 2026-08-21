<?php

namespace App\Enums;

/**
 * What kind of help a support plan describes.
 *
 * Health and counselling are kept apart from learning support because they
 * are read by fewer people.
 */
enum SupportCategory: string
{
    /**
     * A medical need the school must work around.
     */
    case Health = 'health';

    /**
     * Emotional or behavioural help from a counsellor.
     */
    case Counselling = 'counselling';

    /**
     * A change to how a child learns or is examined.
     */
    case Accommodation = 'accommodation';

    /**
     * Extra teaching to close a gap.
     */
    case Intervention = 'intervention';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Health => 'Health',
            self::Counselling => 'Counselling',
            self::Accommodation => 'Accommodation',
            self::Intervention => 'Intervention',
        };
    }

    /**
     * Check if the plan is readable only by the people who run it.
     */
    public function isConfidential(): bool
    {
        return in_array($this, [self::Health, self::Counselling], true);
    }

    /**
     * Get the permission needed to read a plan of this kind.
     */
    public function readPermission(): string
    {
        return $this->isConfidential() ? 'read confidential support plan' : 'read support plan';
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $category): string => $category->value, self::cases());
    }
}
