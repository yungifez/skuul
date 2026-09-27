<?php

namespace App\Enums;

enum SyllabusStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Published = 'published';
    case Superseded = 'superseded';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Awaiting review',
            self::Published => 'Published',
            self::Superseded => 'Superseded',
            self::Archived => 'Archived',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::Published;
    }
}
