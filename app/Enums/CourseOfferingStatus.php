<?php

namespace App\Enums;

enum CourseOfferingStatus: string
{
    case Draft = 'draft';

    case Active = 'active';

    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft    => 'Draft',
            self::Active   => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function canMoveTo(self $status): bool
    {
        return match ($this) {
            self::Draft    => $status === self::Active || $status === self::Archived,
            self::Active   => $status === self::Archived,
            self::Archived => false,
        };
    }
}
