<?php

namespace App\Services\Exam;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Collection;

class ExamService
{
    /**
     * @return Collection<int, Exam>
     */
    public function getAllExamsInAcademicPeriod(int $academicPeriodId): Collection
    {
        return Exam::query()->where('academic_period_id', $academicPeriodId)->get();
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getActiveExamsInAcademicPeriod(int $academicPeriodId): Collection
    {
        return Exam::query()
            ->where('academic_period_id', $academicPeriodId)
            ->where('active', true)
            ->get();
    }

    public function getExamById(int $id): ?Exam
    {
        return Exam::find($id);
    }

    /**
     * @param  array{name: string, description: ?string, academic_period_id: int, start_date: string, stop_date: string}  $attributes
     */
    public function createExam(array $attributes): Exam
    {
        return Exam::create($attributes);
    }

    /**
     * @param  array{name: string, description: ?string, academic_period_id: int, start_date: string, stop_date: string}  $attributes
     */
    public function updateExam(Exam $exam, array $attributes): void
    {
        $exam->update($attributes);
    }

    public function setExamActiveStatus(Exam $exam, bool $active): void
    {
        $exam->update(['active' => $active]);
    }

    public function deleteExam(Exam $exam): void
    {
        $exam->delete();
    }
}
