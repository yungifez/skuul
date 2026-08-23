<?php

namespace App\Enums;

/**
 * What a member of staff does in a boarding house.
 */
enum SupervisionRole: string
{
    case Warden = 'warden';

    case AssistantWarden = 'assistant_warden';

    case Matron = 'matron';

    case NightStaff = 'night_staff';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Warden => 'Warden',
            self::AssistantWarden => 'Assistant warden',
            self::Matron => 'Matron',
            self::NightStaff => 'Night staff',
        };
    }

    /**
     * Check whether this role answers for the whole house.
     */
    public function leadsTheHouse(): bool
    {
        return $this === self::Warden;
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
