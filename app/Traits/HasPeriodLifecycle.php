<?php

namespace App\Traits;

use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicPeriodStatusChange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Shared behaviour for academic years and semesters.
 *
 * Both are academic periods: they open, they close, and the change is kept.
 */
trait HasPeriodLifecycle
{
    /**
     * Get every recorded state change of this period.
     */
    public function statusChanges(): MorphMany
    {
        return $this->morphMany(AcademicPeriodStatusChange::class, 'period')->orderBy('id');
    }

    /**
     * Limit the query to periods that still accept writes.
     *
     * @param  Builder  $query
     */
    public function scopeOpen($query): Builder
    {
        return $query->where('status', AcademicPeriodStatus::Open);
    }

    /**
     * Limit the query to finished periods.
     *
     * @param  Builder  $query
     */
    public function scopeClosed($query): Builder
    {
        return $query->where('status', AcademicPeriodStatus::Closed);
    }

    /**
     * Check if records of this period can still be written.
     */
    public function isOpen(): bool
    {
        return $this->getAttribute('status') === AcademicPeriodStatus::Open;
    }

    /**
     * Check if the period is finished.
     */
    public function isClosed(): bool
    {
        return $this->getAttribute('status') === AcademicPeriodStatus::Closed;
    }

    /**
     * Get the label to show in the interface.
     */
    public function statusLabel(): string
    {
        return $this->getAttribute('status')->label();
    }
}
