<?php

namespace App\Enums;

enum AdmissionWaitlistStatus: string
{
    case Pending = 'pending';
    case Offered = 'offered';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Withdrawn = 'withdrawn';
    case Placed = 'placed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Offered => 'Offer made',
            self::Accepted => 'Accepted',
            self::Declined => 'Declined',
            self::Withdrawn => 'Withdrawn',
            self::Placed => 'Placed',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Pending, self::Offered], true);
    }
}
