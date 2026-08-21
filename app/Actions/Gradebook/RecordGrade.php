<?php

namespace App\Actions\Gradebook;

use App\Enums\GradeEntryState;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\StudentRecord;
use App\Models\User;

/**
 * Write what a student got for one grade item.
 *
 * Entering a mark twice replaces the mark instead of adding a second one, so
 * a teacher can correct a typing mistake while the gradebook is open. Once
 * the period closes, the gradebook closes with it.
 */
class RecordGrade
{
    /**
     * Record the mark or the state.
     *
     * @throws InvalidValueException when the mark does not fit the item
     * @throws ClosedPeriodException when the academic period is closed
     */
    public function record(
        GradeItem $item,
        StudentRecord $enrollment,
        GradeEntryState $state = GradeEntryState::Graded,
        ?float $points = null,
        ?string $scaleValue = null,
        ?string $comment = null,
        ?User $actor = null,
    ): GradeEntry {
        $this->failIfRecordsDoNotFit($item, $enrollment, $state, $points);

        return GradeEntry::updateOrCreate(
            [
                'grade_item_id'     => $item->id,
                'student_record_id' => $enrollment->id,
            ],
            [
                'state'       => $state,
                'points'      => $state->needsPoints() ? $points : null,
                'scale_value' => $scaleValue,
                'comment'     => $comment,
                'graded_by'   => $actor === null ? auth()->id() : $actor->id,
                'graded_at'   => now(),
            ],
        );
    }

    /**
     * Check the mark against the item, the student, and the period.
     *
     * @throws InvalidValueException
     * @throws ClosedPeriodException
     */
    private function failIfRecordsDoNotFit(GradeItem $item, StudentRecord $enrollment, GradeEntryState $state, ?float $points): void
    {
        if ($enrollment->school_id !== null && $enrollment->school_id !== $item->school_id) {
            throw new InvalidValueException('This student is enrolled in another school.');
        }

        $period = $item->semester ?? $item->academicYear;

        if ($period !== null && $period->isClosed()) {
            throw new ClosedPeriodException('You cannot grade in a closed academic period.');
        }

        if (!$state->needsPoints()) {
            return;
        }

        if (!$item->type->carriesPoints()) {
            return;
        }

        if ($points === null) {
            throw new InvalidValueException('Give the mark a number, or say why there is none.');
        }

        if ($points < 0) {
            throw new InvalidValueException('A mark cannot be less than zero.');
        }

        if ($item->max_points !== null && $points > $item->max_points) {
            throw new InvalidValueException("A mark cannot be more than $item->max_points.");
        }
    }
}
