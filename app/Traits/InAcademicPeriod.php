<?php

namespace App\Traits;

use App\Exceptions\ClosedPeriodException;
use App\Models\AcademicYear;
use App\Models\Semester;

/**
 * Freeze a record when the academic period it belongs to is closed.
 *
 * A closing school year must keep its placements, timetables, and results as
 * they were. Every model that belongs to a period says which period governs
 * it, and this trait refuses writes once that period is closed.
 *
 * Correcting frozen work means reopening the period, which is a separate
 * permissioned action that leaves an audit record.
 */
trait InAcademicPeriod
{
    /**
     * Refuse writes once the governing period is closed.
     */
    public static function bootInAcademicPeriod(): void
    {
        static::saving(function (self $model): void {
            $model->failIfPeriodIsClosed('change');
        });

        static::deleting(function (self $model): void {
            $model->failIfPeriodIsClosed('remove');
        });
    }

    /**
     * Stop the write when the governing period is closed.
     *
     * @throws ClosedPeriodException
     */
    public function failIfPeriodIsClosed(string $verb): void
    {
        $period = $this->governingAcademicPeriod();

        if ($period === null || !$period->isClosed()) {
            return;
        }

        throw new ClosedPeriodException(
            'You cannot '.$verb.' this record because its academic period is closed.'
        );
    }

    /**
     * Get the period that governs this record.
     *
     * Override this when the period is reached through another record.
     */
    public function governingAcademicPeriod(): AcademicYear|Semester|null
    {
        /** @var AcademicYear|Semester|null $period */
        $period = $this->getAttribute('semester') ?? $this->getAttribute('academicYear');

        return $period;
    }
}
