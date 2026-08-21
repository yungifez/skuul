<?php

namespace App\Enums;

/**
 * What kind of case an incident is.
 *
 * Ordinary behaviour and safeguarding are kept apart because they are read by
 * different people for different reasons.
 */
enum IncidentCategory: string
{
    /**
     * Everyday behaviour: lateness, disruption, damage.
     */
    case Behaviour = 'behaviour';

    /**
     * A concern about a child's safety or welfare.
     */
    case Safeguarding = 'safeguarding';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Behaviour    => 'Behaviour',
            self::Safeguarding => 'Safeguarding',
        };
    }

    /**
     * Check if the case is restricted to the people who handle it.
     */
    public function isRestricted(): bool
    {
        return $this === self::Safeguarding;
    }

    /**
     * Get the permission needed to read a case of this kind.
     */
    public function readPermission(): string
    {
        return $this->isRestricted() ? 'read safeguarding case' : 'read incident';
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
