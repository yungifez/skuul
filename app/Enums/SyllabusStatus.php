<?php

namespace App\Enums;

enum SyllabusStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Superseded = 'superseded';
    case Archived = 'archived';

    public function isVisible(): bool
    {
        return $this === self::Published;
    }
}
