<?php

namespace App\Services\Finance;

use App\Models\Budget;

/**
 * One plan beside what the books say happened.
 */
class BudgetComparison
{
    public function __construct(
        public readonly Budget $budget,
        public readonly float $planned,
        public readonly float $actual,
    ) {}

    /**
     * Get how far the books are from the plan.
     *
     * A positive number means more happened than was planned.
     */
    public function difference(): float
    {
        return round($this->actual - $this->planned, 2);
    }

    /**
     * Get how much of the plan has been used, as a percentage.
     *
     * A plan of nothing cannot be used up, so it has no answer.
     */
    public function used(): ?float
    {
        return $this->planned > 0 ? round($this->actual / $this->planned * 100, 1) : null;
    }

    /**
     * Check whether the books have passed the plan.
     */
    public function isOverspent(): bool
    {
        return $this->actual > $this->planned;
    }
}
