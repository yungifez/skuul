<?php

namespace App\Services\Report;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ReportCardSnapshot;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportCardDirectory
{
    /** @return Collection<int, StudentRecord> */
    public function students(): Collection
    {
        return StudentRecord::query()
            ->inSchool()
            ->with('user:id,name')
            ->orderBy('admission_number')
            ->get(['id', 'user_id', 'admission_number']);
    }

    /** @return Collection<int, AcademicYear> */
    public function academicYears(): Collection
    {
        return AcademicYear::query()
            ->inSchool()
            ->orderByDesc('start_year')
            ->orderByDesc('id')
            ->get();
    }

    public function academicYear(int $academicYearId): ?AcademicYear
    {
        return AcademicYear::query()->inSchool()->find($academicYearId);
    }

    /** @return Collection<int, AcademicPeriod> */
    public function periods(?AcademicYear $academicYear = null): Collection
    {
        return AcademicPeriod::query()
            ->inSchool()
            ->when($academicYear !== null, function (Builder $query) use ($academicYear): void {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->with('academicYear:id,start_year,stop_year')
            ->ordered()
            ->get(['id', 'name', 'label', 'academic_year_id']);
    }

    public function academicPeriod(int $academicPeriodId): ?AcademicPeriod
    {
        return AcademicPeriod::query()->inSchool()->find($academicPeriodId);
    }

    public function reportCards(
        ?int $studentRecordId,
        ?AcademicYear $academicYear,
        ?AcademicPeriod $academicPeriod,
    ): LengthAwarePaginator {
        return ReportCardSnapshot::query()
            ->inSchool()
            ->with([
                'studentRecord.user:id,name',
                'academicYear:id,start_year,stop_year',
                'academicPeriod:id,name,label',
            ])
            ->when($studentRecordId !== null, function (Builder $query) use ($studentRecordId): void {
                $query->where('student_record_id', $studentRecordId);
            })
            ->when($academicYear !== null, function (Builder $query) use ($academicYear): void {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->when($academicPeriod !== null, function (Builder $query) use ($academicPeriod): void {
                $query->where('academic_period_id', $academicPeriod->id);
            })
            ->latest('published_at')
            ->paginate(20);
    }
}
