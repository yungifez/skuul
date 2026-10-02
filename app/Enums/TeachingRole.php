<?php

namespace App\Enums;

/**
 * The part a teacher takes in a subject.
 *
 * Several teachers can share one subject, so each assignment says what that
 * teacher does in it.
 */
enum TeachingRole: string
{
    /**
     * The teacher who is responsible for the subject.
     */
    case Lead = 'lead';

    /**
     * A teacher who supports the lead teacher.
     */
    case Supporting = 'supporting';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Lead => 'Lead teacher',
            self::Supporting => 'Supporting teacher',
        };
    }

    /**
     * Get the values a form may send.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
    }
}
