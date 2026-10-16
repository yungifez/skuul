<?php

namespace App\Traits;

use App\Exceptions\ClosedPeriodException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;

/**
 * Refuse writes to records in frozen academic periods.
 * Corrections require an authorized, audited reopen.
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
    public function governingAcademicPeriod(): AcademicYear|AcademicPeriod|null
    {
        /** @var AcademicYear|AcademicPeriod|null $period */
        $period = $this->getAttribute('academicPeriod') ?? $this->getAttribute('academicYear');

        return $period;
    }
}
