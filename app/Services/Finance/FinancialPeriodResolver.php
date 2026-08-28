<?php

namespace App\Services\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\FinancialPeriod;
use Carbon\CarbonInterface;

class FinancialPeriodResolver
{
    /**
     * Find the period that owns a finance date and still accepts writes.
     *
     * @throws InvalidValueException when the date is outside an open period
     */
    public function openFor(int $schoolId, CarbonInterface|string $date): FinancialPeriod
    {
        $date = $date instanceof CarbonInterface ? $date : now()->parse($date);
        $period = FinancialPeriod::query()
            ->inSchool($schoolId)
            ->whereDate('starts_on', '<=', $date)
            ->whereDate('ends_on', '>=', $date)
            ->first();

        if ($period === null) {
            throw new InvalidValueException('Create a financial period for this date before recording finance activity.');
        }

        if (!$period->isOpen()) {
            throw new InvalidValueException("Financial period {$period->name} is closed.");
        }

        return $period;
    }

    /**
     * Find the open period that covers today, or the nearest open period.
     */
    public function currentOpen(int $schoolId): ?FinancialPeriod
    {
        $period = FinancialPeriod::query()
            ->inSchool($schoolId)
            ->open()
            ->whereDate('starts_on', '<=', now())
            ->whereDate('ends_on', '>=', now())
            ->orderByDesc('starts_on')
            ->first();

        return $period ?? FinancialPeriod::query()
            ->inSchool($schoolId)
            ->open()
            ->orderByDesc('starts_on')
            ->first();
    }
}
