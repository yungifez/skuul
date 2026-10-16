<?php

namespace App\Services\Calendar;

/**
 * One thing a period needs before it can close.
 *
 * A finding is advice, not a rule. A school may close a period that still has
 * findings; the close records what was outstanding at the time.
 */
readonly class ClosureFinding
{
    /**
     * @param  string  $key  a stable name for the check, for tests and storage
     * @param  string  $summary  what a person reads
     * @param  int  $count  how many records are outstanding
     * @param  bool  $blocking  whether the check stops an unforced close
     */
    public function __construct(
        public string $key,
        public string $summary,
        public int $count,
        public bool $blocking,
    ) {}

    /**
     * Get the finding as an array, for the audit record and the checklist.
     *
     * @return array{key: string, summary: string, count: int, blocking: bool}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'summary' => $this->summary,
            'count' => $this->count,
            'blocking' => $this->blocking,
        ];
    }
}
