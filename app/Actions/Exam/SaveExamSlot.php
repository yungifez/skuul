<?php

namespace App\Actions\Exam;

use App\Exceptions\InvalidValueException;
use App\Models\Exam;
use App\Models\ExamSlot;
use Illuminate\Support\Facades\DB;

/**
 * Add a paper to an exam, or rename it and change its marks.
 *
 * One exam never holds two papers with the same name, so a timetable and a
 * mark sheet always point at one paper.
 */
class SaveExamSlot
{
    /**
     * Add a paper to an exam.
     *
     * @param  array{name: string, description: string|null, total_marks: int}  $attributes
     *
     * @throws InvalidValueException when the exam already has a paper with that name
     */
    public function create(Exam $exam, array $attributes): ExamSlot
    {
        return DB::transaction(function () use ($exam, $attributes): ExamSlot {
            Exam::query()->whereKey($exam->getKey())->lockForUpdate()->firstOrFail();
            $this->refuseATakenName($exam, $attributes['name']);

            return $exam->examSlots()->create($attributes);
        });
    }

    /**
     * Rename a paper, describe it again, or change its marks.
     *
     * @param  array{name: string, description: string|null, total_marks: int}  $attributes
     *
     * @throws InvalidValueException when another paper of the exam has that name
     */
    public function update(ExamSlot $examSlot, array $attributes): ExamSlot
    {
        return DB::transaction(function () use ($examSlot, $attributes): ExamSlot {
            $exam = Exam::query()->whereKey($examSlot->exam_id)->lockForUpdate()->firstOrFail();
            $this->refuseATakenName($exam, $attributes['name'], $examSlot);

            $examSlot->update($attributes);

            return $examSlot;
        });
    }

    /**
     * @throws InvalidValueException when the exam already has a paper with that name
     */
    private function refuseATakenName(Exam $exam, string $name, ?ExamSlot $except = null): void
    {
        $isTaken = $exam->examSlots()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($except !== null, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException("{$exam->name} already has a paper with that name.");
        }
    }
}
