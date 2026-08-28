<?php

namespace App\Services\Academic;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Models\UserAcademicPeriodPreference;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * The academic year and academic period the current request works in.
 *
 * The working period belongs to the staff member, not to the school record,
 * so two people can work in different periods of the same school at the same
 * time. The school calendar still determines which period is current today.
 *
 * Every academic record still carries its own period. This context only says
 * which period the screen is showing.
 */
class AcademicPeriodContext
{
    /**
     * The session key that remembers the academic year.
     */
    public const YEAR_SESSION_KEY = 'active_academic_year_id';

    private ?AcademicYear $academicYear = null;

    private ?AcademicPeriod $academicPeriod = null;

    private ?string $resolutionError = null;

    private bool $resolved = false;

    /**
     * Get the academic year being worked in, or null when none is set.
     */
    public function academicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    /**
     * Get the id of the academic year being worked in.
     */
    public function academicYearId(): ?int
    {
        return $this->academicYear?->id;
    }

    /**
     * Get the academic period being worked in, or null when none is set.
     */
    public function academicPeriod(): ?AcademicPeriod
    {
        return $this->academicPeriod;
    }

    /**
     * Get the id of the academic period being worked in.
     */
    public function academicPeriodId(): ?int
    {
        return $this->academicPeriod?->id;
    }

    /**
     * Get a calendar-resolution problem that needs staff attention.
     */
    public function resolutionError(): ?string
    {
        return $this->resolutionError;
    }

    /**
     * Get the academic year or fail.
     *
     * Use this before a write that must name its period.
     */
    public function academicYearOrFail(): AcademicYear
    {
        if ($this->academicYear === null) {
            throw new RuntimeException('No academic year is set for this request. Set a working academic year first.');
        }

        return $this->academicYear;
    }

    /**
     * Get the academic period or fail.
     */
    public function academicPeriodOrFail(): AcademicPeriod
    {
        if ($this->academicPeriod === null) {
            throw new RuntimeException('No academic period is set for this request. Set a working academic period first.');
        }

        return $this->academicPeriod;
    }

    /**
     * Set the academic year for this request and remember it.
     *
     * Changing the year clears an academic period that does not belong to it.
     */
    public function setAcademicYear(?AcademicYear $academicYear, bool $remember = true): void
    {
        $this->academicYear = $academicYear;
        $this->resolved = true;

        if ($this->academicPeriod !== null && $this->academicPeriod->academic_year_id !== $academicYear?->id) {
            $this->setAcademicPeriod(null);
        }

        $this->remember(self::YEAR_SESSION_KEY, $academicYear?->id, $remember);
    }

    /**
     * Set the academic period for this request.
     */
    public function setAcademicPeriod(?AcademicPeriod $academicPeriod): void
    {
        $this->academicPeriod = $academicPeriod;
    }

    /**
     * Clear the working period.
     */
    public function forget(): void
    {
        $this->academicYear = null;
        $this->academicPeriod = null;
        $this->resolutionError = null;
        $this->resolved = false;
    }

    /**
     * Check if the request already worked out its period.
     */
    public function isResolved(): bool
    {
        return $this->resolved;
    }

    /**
     * Work out which period this request belongs to and set it.
     *
     * A saved staff choice wins. When there is no saved choice, staff
     * automatically start in the calendar period that covers today, then fall
     * back to the school default when the calendar has no current period.
     */
    public function resolveFor(School $school, User $user, ?Request $request = null): void
    {
        $year = $this->allowedAcademicYear($school, $request?->session()?->get(self::YEAR_SESSION_KEY))
            ?? $this->savedAcademicYearFor($user, $school)
            ?? $school->academicYear;

        $this->academicYear = $year;

        $this->resolutionError = null;
        $coveringPeriods = $year?->periodsForDate() ?? collect();

        if ($coveringPeriods->count() > 1) {
            $periodNames = $coveringPeriods->pluck('displayName')->implode(', ');
            $this->resolutionError = "The calendar has overlapping reporting periods: {$periodNames}. Fix their dates before continuing.";
            $academicPeriod = null;
        } else {
            $academicPeriod = $year === null ? null : $this->savedPeriodFor($user, $school, $year);

            // A staff member with no explicit choice follows the calendar.
            $academicPeriod ??= $year?->periodForDate();
        }

        if ($this->resolutionError === null && $academicPeriod === null && $year !== null && $school->academic_period_id !== null) {
            $academicPeriod = $this->allowedAcademicPeriod($year, $school->academic_period_id);
        }

        $this->academicPeriod = $academicPeriod;
        $this->resolved = true;
    }

    /**
     * Return the academic year when it belongs to the school, else null.
     */
    public function allowedAcademicYear(School $school, int|string|null $academicYearId): ?AcademicYear
    {
        if ($academicYearId === null) {
            return null;
        }

        return AcademicYear::where('school_id', $school->id)->find((int) $academicYearId);
    }

    /**
     * Return the academic period when it belongs to the academic year, else null.
     */
    public function allowedAcademicPeriod(AcademicYear $academicYear, int|string|null $academicPeriodId): ?AcademicPeriod
    {
        if ($academicPeriodId === null) {
            return null;
        }

        return AcademicPeriod::where('academic_year_id', $academicYear->id)->find((int) $academicPeriodId);
    }

    /**
     * Get the staff member's most recently used academic year for this school.
     */
    private function savedAcademicYearFor(User $user, School $school): ?AcademicYear
    {
        return UserAcademicPeriodPreference::query()
            ->inSchool($school)
            ->whereBelongsTo($user)
            ->whereBelongsTo($school)
            ->with('academicYear')
            ->latest('updated_at')
            ->first()
            ?->academicYear;
    }

    /**
     * Get the staff member's saved working period for this school and year.
     */
    private function savedPeriodFor(User $user, School $school, AcademicYear $academicYear): ?AcademicPeriod
    {
        return UserAcademicPeriodPreference::query()
            ->inSchool($school)
            ->whereBelongsTo($user)
            ->whereBelongsTo($school)
            ->whereBelongsTo($academicYear)
            ->whereHas('academicPeriod', fn ($query) => $query
                ->where('school_id', $school->id)
                ->where('academic_year_id', $academicYear->id))
            ->with('academicPeriod')
            ->first()
            ?->academicPeriod;
    }

    /**
     * Keep the choice in the session when one is available.
     */
    private function remember(string $key, ?int $value, bool $remember): void
    {
        if (!$remember || !app()->bound('session') || !app('session')->isStarted()) {
            return;
        }

        $value === null
            ? session()->forget($key)
            : session()->put($key, $value);
    }
}
