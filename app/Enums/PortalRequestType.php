<?php

namespace App\Enums;

/**
 * What a family is asking the school for.
 */
enum PortalRequestType: string
{
    /**
     * A copy of a document, such as a result slip or a letter.
     */
    case Document = 'document';

    /**
     * A change to something the family believes is wrong.
     */
    case Correction = 'correction';

    /**
     * A time to speak to somebody at the school.
     */
    case Appointment = 'appointment';

    /**
     * A note saying the family read something.
     */
    case Acknowledgement = 'acknowledgement';

    /**
     * Get the label to show in the interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Document        => 'Document request',
            self::Correction      => 'Correction request',
            self::Appointment     => 'Appointment request',
            self::Acknowledgement => 'Acknowledgement',
        };
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
