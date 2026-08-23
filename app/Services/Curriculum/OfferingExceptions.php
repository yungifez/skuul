<?php

namespace App\Services\Curriculum;

use App\Enums\RosterMode;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelException;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The subjects a campus has allowed to be taught outside its model.
 */
class OfferingExceptions
{
    /**
     * Check whether one subject may use a roster the campus model refuses.
     *
     * An exception that names no level covers every level, because a campus
     * that runs one combined music class usually runs it for all of them.
     */
    public function allows(
        AcademicYear $academicYear,
        Subject $subject,
        AcademicLevel $academicLevel,
        RosterMode $rosterMode,
    ): bool {
        return InstructionalModelException::query()
            ->running()
            ->where('school_id', $academicYear->school_id)
            ->where('academic_year_id', $academicYear->id)
            ->where('subject_id', $subject->id)
            ->where('roster_mode', $rosterMode)
            ->where(function (Builder $level) use ($academicLevel): void {
                $level->whereNull('academic_level_id')
                    ->orWhere('academic_level_id', $academicLevel->id);
            })
            ->exists();
    }

    /**
     * Get the exceptions of one cycle, newest first.
     *
     * @return Collection<int, InstructionalModelException>
     */
    public function forCycle(AcademicYear $academicYear): Collection
    {
        return InstructionalModelException::query()
            ->where('school_id', $academicYear->school_id)
            ->where('academic_year_id', $academicYear->id)
            ->with(['subject', 'academicLevel', 'grantedBy'])
            ->orderByDesc('id')
            ->get()
            ->toBase();
    }
}
