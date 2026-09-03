<?php

namespace App\Services\Ranking;

use App\Enums\Feature;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Cohort;
use App\Models\CourseOffering;
use App\Models\ResultSnapshot;
use App\Models\StudentRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Put a group of students in order by their published results.
 *
 * A position is worked out when it is asked for. It is never a field on a
 * student, an enrollment, or a grade, so nothing has to be rewritten when a
 * result is corrected. Two equal averages share a position.
 */
class ResultRanking
{
    /**
     * Rank everybody in a cohort.
     *
     * @return Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>
     */
    public function forCohort(Cohort $cohort, ?int $academicYearId = null, ?int $academicPeriodId = null, ?CourseOffering $courseOffering = null, ?int $subjectId = null, ?array $courseOfferingIds = null): Collection
    {
        $enrollmentIds = $cohort->members()->current()->whereNotNull('student_record_id')->pluck('student_record_id');

        return $this->rank($enrollmentIds->all(), $academicYearId, $academicPeriodId, $courseOffering, $subjectId, $courseOfferingIds);
    }

    /**
     * Rank everybody placed in one cycle section.
     *
     * @return Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>
     */
    public function forCycleSection(AcademicCycleSection $cycleSection, ?int $academicYearId = null, ?int $academicPeriodId = null, ?CourseOffering $courseOffering = null, ?int $subjectId = null, ?array $courseOfferingIds = null): Collection
    {
        $enrollmentIds = StudentRecord::query()
            ->inSchool()
            ->where('academic_cycle_section_id', $cycleSection->id)
            ->pluck('id');

        return $this->rank($enrollmentIds->all(), $academicYearId, $academicPeriodId, $courseOffering, $subjectId, $courseOfferingIds);
    }

    /**
     * Rank everybody placed in one academic level or one of its child levels.
     *
     * @return Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>
     */
    public function forAcademicLevel(AcademicLevel $academicLevel, ?int $academicYearId = null, ?int $academicPeriodId = null, ?CourseOffering $courseOffering = null, ?int $subjectId = null, ?array $courseOfferingIds = null): Collection
    {
        $levelIds = $academicLevel->teachingScopeIds();
        $enrollmentIds = StudentRecord::query()
            ->inSchool()
            ->whereHas('academicCycleSection', function (Builder $query) use ($levelIds): void {
                $query->whereIn('academic_level_id', $levelIds);
            })
            ->pluck('id');

        return $this->rank($enrollmentIds->all(), $academicYearId, $academicPeriodId, $courseOffering, $subjectId, $courseOfferingIds);
    }

    /**
     * Put the given enrollments in order.
     *
     * @param  array<int, int>  $enrollmentIds
     * @return Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>
     *
     * @throws InvalidValueException when the school turned ranking off
     */
    public function rank(array $enrollmentIds, ?int $academicYearId = null, ?int $academicPeriodId = null, ?CourseOffering $courseOffering = null, ?int $subjectId = null, ?array $courseOfferingIds = null): Collection
    {
        if (!feature_enabled(Feature::Ranking)) {
            throw new InvalidValueException('This school does not rank students.');
        }

        if ($enrollmentIds === []) {
            return collect();
        }

        $averages = $this->averages($enrollmentIds, $academicYearId, $academicPeriodId, $courseOffering, $subjectId, $courseOfferingIds);

        return $this->withPositions($averages);
    }

    /**
     * Get one average for each student, from the newest revision of each result.
     *
     * @param  array<int, int>  $enrollmentIds
     * @return array<int, array{student_record_id: int, average: float, subjects: int}>
     */
    private function averages(array $enrollmentIds, ?int $academicYearId, ?int $academicPeriodId, ?CourseOffering $courseOffering, ?int $subjectId, ?array $courseOfferingIds): array
    {
        $snapshots = ResultSnapshot::query()
            ->approved()
            ->whereIn('student_record_id', $enrollmentIds)
            ->when($academicYearId !== null, fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('academic_year_id', $academicYearId)))
            ->when($academicPeriodId !== null, fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('academic_period_id', $academicPeriodId)))
            ->when($courseOffering !== null, fn ($query) => $query->whereBelongsTo($courseOffering))
            ->when($subjectId !== null, fn ($query) => $query->whereHas('courseOffering', fn ($query) => $query->where('subject_id', $subjectId)))
            ->when($courseOfferingIds !== null, fn ($query) => $query->whereIn('course_offering_id', $courseOfferingIds))
            ->whereNotNull('percentage')
            ->orderBy('student_record_id')
            ->get();

        return $snapshots
            ->groupBy('student_record_id')
            ->map(function (Collection $rows, int|string $studentRecordId): array {
                // One offering counts once, at its newest revision.
                $newest = $rows
                    ->groupBy(fn (ResultSnapshot $snapshot): string => (string) $snapshot->course_offering_id)
                    ->map(fn (Collection $offeringRows): ResultSnapshot => $offeringRows->sortByDesc('revision')->first());

                return [
                    'student_record_id' => (int) $studentRecordId,
                    'average' => round((float) $newest->avg('percentage'), 2),
                    'subjects' => (int) $newest->count(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Give each row its position, letting equal averages share one.
     *
     * @param  array<int, array{student_record_id: int, average: float, subjects: int}>  $averages
     * @return Collection<int, array{student_record_id: int, average: float, subjects: int, position: int}>
     */
    private function withPositions(array $averages): Collection
    {
        $sorted = collect($averages)->sortByDesc('average')->values();
        $position = 0;
        $seen = 0;
        $previous = null;

        return $sorted->map(function (array $row) use (&$position, &$seen, &$previous): array {
            $seen++;

            if ($previous === null || $row['average'] < $previous) {
                $position = $seen;
                $previous = $row['average'];
            }

            $row['position'] = (int) $position;

            return $row;
        });
    }
}
