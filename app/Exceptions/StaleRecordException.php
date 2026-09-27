<?php

namespace App\Exceptions;

/**
 * Somebody else changed a record while this person was working on it.
 */
class StaleRecordException extends ApplicationException
{
    /**
     * Create the exception from the fields that somebody else changed.
     *
     * @param  array<int, string>  $fields
     */
    public function __construct(private array $fields)
    {
        parent::__construct('Somebody else changed this record while you worked on it.');
    }

    /**
     * Get every field that somebody else changed.
     *
     * @return array<int, string>
     */
    public function fields(): array
    {
        return $this->fields;
    }
}
