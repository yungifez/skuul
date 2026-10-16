<?php

namespace App\Enums;

/**
 * Where a teacher's lesson note is in its review by the head of department.
 */
enum LessonNoteStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Awaiting review',
            self::Approved => 'Approved',
            self::Returned => 'Sent back',
        };
    }

    /**
     * Check if the author can still change the note.
     */
    public function isEditable(): bool
    {
        return $this === self::Draft || $this === self::Returned;
    }
}
