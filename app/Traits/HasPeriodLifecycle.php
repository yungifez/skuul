<?php

namespace App\Traits;

use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicPeriodStatusChange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Shared behaviour for academic years and academic periods.
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
     * Limit the query to finished periods, archived ones included.
     *
     * @param  Builder  $query
     */
    public function scopeClosed($query): Builder
    {
        return $query->whereIn('status', [
            AcademicPeriodStatus::Closed->value,
            AcademicPeriodStatus::Archived->value,
        ]);
    }

    /**
     * Limit the query to periods kept for history only.
     *
     * @param  Builder  $query
     */
    public function scopeArchived($query): Builder
    {
        return $query->where('status', AcademicPeriodStatus::Archived);
    }

    /**
     * Limit the query to periods routine operations may still write to.
     *
     * @param  Builder  $query
     */
    public function scopeOperational($query): Builder
    {
        return $query->whereIn('status', [
            AcademicPeriodStatus::Open->value,
            AcademicPeriodStatus::Closing->value,
        ]);
    }

    /**
     * Limit the query to periods that are dated but have not started.
     *
     * @param  Builder  $query
     */
    public function scopeScheduled($query): Builder
    {
        return $query->where('status', AcademicPeriodStatus::Scheduled);
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
     *
     * An archived period is closed too: it stopped accepting work when it was
     * closed, and archiving only moved it out of the way.
     */
    public function isClosed(): bool
    {
        return $this->getAttribute('status')->isFrozen();
    }

    /**
     * Check if the period is finishing, so only started work may continue.
     */
    public function isClosing(): bool
    {
        return $this->getAttribute('status') === AcademicPeriodStatus::Closing;
    }

    /**
     * Check if the period is kept for history only.
     */
    public function isArchived(): bool
    {
        return $this->getAttribute('status') === AcademicPeriodStatus::Archived;
    }

    /**
     * Check if routine operations run against this period.
     */
    public function isOperational(): bool
    {
        return $this->getAttribute('status')->isOperational();
    }

    /**
     * Check if the period accepts work that did not exist before.
     */
    public function acceptsNewWork(): bool
    {
        return $this->getAttribute('status')->acceptsNewWork();
    }

    /**
     * Get the label to show in the interface.
     */
    public function statusLabel(): string
    {
        return $this->getAttribute('status')->label();
    }
}
