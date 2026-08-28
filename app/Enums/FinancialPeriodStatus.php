<?php

namespace App\Enums;

enum FinancialPeriodStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }

    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}
