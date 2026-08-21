<?php

namespace App\Enums;

/**
 * Why a person appears in a case.
 */
enum IncidentParticipantRole: string
{
    /**
     * The person the case is about.
     */
    case Subject = 'subject';

    /**
     * The person the incident happened to.
     */
    case AffectedParty = 'affected_party';

    /**
     * Somebody who saw it.
     */
    case Witness = 'witness';

    /**
     * The person who wrote the case down.
     */
    case Reporter = 'reporter';

    /**
     * Somebody handling the case.
     */
    case Handler = 'handler';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Subject => 'Subject of the case',
            self::AffectedParty => 'Affected party',
            self::Witness => 'Witness',
            self::Reporter => 'Reporter',
            self::Handler => 'Handler',
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
