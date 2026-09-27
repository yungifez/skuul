<?php

namespace App\Actions\Exam;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\Exam;
use App\Models\GradeItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Plan an exam in a reporting period, or change its name and dates.
 *
 * An exam sits inside its period, and one period never holds two exams with
 * the same name, so a report card never lists "Mid-term" twice.
 */
class SaveExam
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Plan an exam.
     *
     * @param  array{name: string, description: string|null, academic_period_id: int, start_date: string, stop_date: string}  $attributes
     *
     * @throws InvalidValueException when the dates or the name do not fit the period
     */
    public function create(array $attributes, User $actor): Exam
    {
        return DB::transaction(function () use ($attributes, $actor): Exam {
            $period = $this->lockedPeriod($attributes['academic_period_id']);
            $this->refuseDatesOutside($period, $attributes);
            $this->refuseATakenName($period, $attributes['name']);

            $exam = Exam::create($attributes);

            $this->audit->record(AuditAction::ExamChanged, $exam, ['created' => true], $actor);

            return $exam;
        });
    }

    /**
     * Change an exam's name, dates or period.
     *
     * @param  array{name: string, description: string|null, academic_period_id: int, start_date: string, stop_date: string}  $attributes
     *
     * @throws InvalidValueException when the dates or the name do not fit the period, or its papers are already marked
     */
    public function update(Exam $exam, array $attributes, User $actor): Exam
    {
        return DB::transaction(function () use ($exam, $attributes, $actor): Exam {
            $exam = Exam::query()->whereKey($exam->getKey())->lockForUpdate()->firstOrFail();
            $period = $this->lockedPeriod($attributes['academic_period_id']);

            $isMoving = $period->getKey() !== $exam->academic_period_id;

            // The gradebook columns of a paper belong to the classes of the old
            // period, so moving the exam would leave its marks in the wrong term.
            if ($isMoving && GradeItem::query()->whereIn('exam_slot_id', $exam->examSlots()->select('id'))->exists()) {
                throw new InvalidValueException('Papers in this exam are already in the gradebook, so it stays in its reporting period.');
            }

            $this->refuseDatesOutside($period, $attributes);
            $this->refuseATakenName($period, $attributes['name'], $exam);

            $exam->fill($attributes);

            if (!$exam->isDirty()) {
                return $exam;
            }

            $changed = array_keys($exam->getDirty());
            $exam->save();

            $this->audit->record(AuditAction::ExamChanged, $exam, ['changed' => $changed], $actor);

            return $exam;
        });
    }

    private function lockedPeriod(int $periodId): AcademicPeriod
    {
        return AcademicPeriod::query()->inSchool()->lockForUpdate()->findOrFail($periodId);
    }

    /**
     * @param  array{start_date: string, stop_date: string}  $attributes
     *
     * @throws InvalidValueException when the exam starts before or ends after its period
     */
    private function refuseDatesOutside(AcademicPeriod $period, array $attributes): void
    {
        $startsOn = Carbon::parse($attributes['start_date'])->startOfDay();
        $stopsOn = Carbon::parse($attributes['stop_date'])->startOfDay();

        if ($stopsOn->lt($startsOn)) {
            throw new InvalidValueException('An exam cannot end before it starts.');
        }

        if ($period->starts_on !== null && $startsOn->lt($period->starts_on->copy()->startOfDay())) {
            throw new InvalidValueException("The exam starts before {$period->displayName} starts on {$period->starts_on->format('j M Y')}.");
        }

        if ($period->ends_on !== null && $stopsOn->gt($period->ends_on->copy()->startOfDay())) {
            throw new InvalidValueException("The exam ends after {$period->displayName} ends on {$period->ends_on->format('j M Y')}.");
        }
    }

    /**
     * @throws InvalidValueException when the period already has an exam with that name
     */
    private function refuseATakenName(AcademicPeriod $period, string $name, ?Exam $except = null): void
    {
        $isTaken = Exam::query()
            ->where('academic_period_id', $period->getKey())
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($except !== null, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException("{$period->displayName} already has an exam with that name.");
        }
    }
}
