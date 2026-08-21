<?php

namespace App\Services\Academic;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * The academic year and semester the current request works in.
 *
 * The working period belongs to the request, not to the school record. The
 * pointers on the school row are only the default a person starts in, so two
 * people can work in different periods of the same school at the same time.
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

    /**
     * The session key that remembers the semester.
     */
    public const SEMESTER_SESSION_KEY = 'active_semester_id';

    private ?AcademicYear $academicYear = null;

    private ?Semester $semester = null;

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
     * Get the semester being worked in, or null when none is set.
     */
    public function semester(): ?Semester
    {
        return $this->semester;
    }

    /**
     * Get the id of the semester being worked in.
     */
    public function semesterId(): ?int
    {
        return $this->semester?->id;
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
     * Get the semester or fail.
     */
    public function semesterOrFail(): Semester
    {
        if ($this->semester === null) {
            throw new RuntimeException('No semester is set for this request. Set a working semester first.');
        }

        return $this->semester;
    }

    /**
     * Set the academic year for this request and remember it.
     *
     * Changing the year clears a semester that does not belong to it.
     */
    public function setAcademicYear(?AcademicYear $academicYear, bool $remember = true): void
    {
        $this->academicYear = $academicYear;
        $this->resolved = true;

        if ($this->semester !== null && $this->semester->academic_year_id !== $academicYear?->id) {
            $this->setSemester(null, $remember);
        }

        $this->remember(self::YEAR_SESSION_KEY, $academicYear?->id, $remember);
    }

    /**
     * Set the semester for this request and remember it.
     */
    public function setSemester(?Semester $semester, bool $remember = true): void
    {
        $this->semester = $semester;

        $this->remember(self::SEMESTER_SESSION_KEY, $semester?->id, $remember);
    }

    /**
     * Clear the working period.
     */
    public function forget(): void
    {
        $this->academicYear = null;
        $this->semester = null;
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
     * The remembered period wins when it still belongs to the school being
     * worked in. Otherwise the person starts in the school default.
     */
    public function resolveFor(School $school, ?Request $request = null): void
    {
        $year = $this->allowedAcademicYear($school, $request?->session()?->get(self::YEAR_SESSION_KEY))
            ?? $school->academicYear;

        $this->academicYear = $year;

        $semester = $year === null
            ? null
            : $this->allowedSemester($year, $request?->session()?->get(self::SEMESTER_SESSION_KEY));

        if ($semester === null && $year !== null && $school->semester_id !== null) {
            $semester = $this->allowedSemester($year, $school->semester_id);
        }

        // Nothing was chosen. Use the period that covers today, when the
        // calendar names one.
        $semester ??= $year?->periodForDate();

        $this->semester = $semester;
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
     * Return the semester when it belongs to the academic year, else null.
     */
    public function allowedSemester(AcademicYear $academicYear, int|string|null $semesterId): ?Semester
    {
        if ($semesterId === null) {
            return null;
        }

        return Semester::where('academic_year_id', $academicYear->id)->find((int) $semesterId);
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
