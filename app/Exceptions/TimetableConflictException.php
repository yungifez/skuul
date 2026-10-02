<?php

namespace App\Exceptions;

/**
 * Raised when a timetable cannot be published because entries clash.
 */
class TimetableConflictException extends ApplicationException
{
    /**
     * Create the exception from the conflicts that were found.
     *
     * @param  array<int, string>  $conflicts
     */
    public function __construct(private array $conflicts)
    {
        parent::__construct('This timetable cannot be published: '.implode(' ', $conflicts));
    }

    /**
     * Get every conflict that stopped publication.
     *
     * @return array<int, string>
     */
    public function conflicts(): array
    {
        return $this->conflicts;
    }
}
