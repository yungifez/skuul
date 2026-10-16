<?php

namespace App\Services\Gradebook;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Find gradebooks in an academic year and period the reader may access.
 */
class GradebookDirectory
{
    /** @return Collection<int, AcademicYear> */
    public function academicYears(): Collection
    {
        return AcademicYear::query()
            ->inSchool()
            ->with('topLevelPeriods')
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->get();
    }

    public function academicYear(int $academicYearId): ?AcademicYear
    {
        return AcademicYear::query()
            ->inSchool()
            ->with('topLevelPeriods')
            ->find($academicYearId);
    }

    public function academicPeriod(AcademicYear $academicYear, int $academicPeriodId): ?AcademicPeriod
    {
        return $academicYear->topLevelPeriods->firstWhere('id', $academicPeriodId);
    }

    public function courseOfferings(AcademicYear $academicYear, ?AcademicPeriod $academicPeriod, User $reader): LengthAwarePaginator
    {
        return CourseOffering::query()
            ->inSchool()
            ->where('academic_year_id', $academicYear->id)
            ->when(
                $academicPeriod !== null,
                fn (Builder $query): Builder => $query->where('academic_period_id', $academicPeriod->id),
            )
            ->when(
                !$reader->can('update subject'),
                fn (Builder $query): Builder => $query->whereHas(
                    'teachingAssignments',
                    fn (Builder $assignments): Builder => $assignments->where('user_id', $reader->id),
                ),
            )
            ->with([
                'academicLevel:id,name',
                'academicPeriod:id,name,label,status',
                'cycleSections:id,name,label',
                'subject:id,name,short_name',
            ])
            ->orderBy('academic_level_id')
            ->orderBy('subject_id')
            ->paginate(25);
    }
}
