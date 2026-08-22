<?php

namespace App\Actions\Gradebook;

use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Exceptions\ClosedPeriodException;
use App\Exceptions\InvalidValueException;
use App\Models\GradeEntry;
use App\Models\GradeItem;
use App\Models\GradingScaleOption;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Gradebook\CourseOfferingRoster;

/**
 * Write what a student got for one grade item.
 *
 * Entering a mark twice replaces the mark instead of adding a second one, so
 * a teacher can correct a typing mistake while the gradebook is open. Once
 * the period closes, the gradebook closes with it.
 */
class RecordGrade
{
    public function __construct(private CourseOfferingRoster $roster)
    {
    }

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
        ?int $gradingScaleOptionId = null,
        ?string $comment = null,
        ?User $actor = null,
    ): GradeEntry {
        $scaleOption = $this->scaleOptionFor($item, $state, $points, $gradingScaleOptionId);
        $recordedPoints = $scaleOption === null ? $points : $scaleOption->points;

        $this->failIfRecordsDoNotFit($item, $enrollment, $state, $recordedPoints);

        return GradeEntry::updateOrCreate(
            [
                'grade_item_id'     => $item->id,
                'student_record_id' => $enrollment->id,
            ],
            [
                'state'                   => $state,
                'points'                  => $state->needsPoints() ? $recordedPoints : null,
                'grading_scale_option_id' => $state->needsPoints() ? $scaleOption?->id : null,
                'comment'                 => $comment,
                'graded_by'               => $actor === null ? auth()->id() : $actor->id,
                'graded_at'               => now(),
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
        $item->loadMissing([
            'courseOffering.academicPeriod',
            'courseOffering.academicYear',
        ]);
        $courseOffering = $item->courseOffering;

        $this->roster->ensureIncludes($courseOffering, $enrollment);

        $period = $courseOffering->academicPeriod ?? $courseOffering->academicYear;

        if ($period !== null && $period->isClosed()) {
            throw new ClosedPeriodException('You cannot grade in a closed academic period.');
        }

        if (!$state->needsPoints()) {
            return;
        }

        if (!$item->type->carriesPoints() || $item->type === GradeItemType::Scale) {
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

    /**
     * Resolve the selected level and make sure it belongs to this exact item scale.
     *
     * @throws InvalidValueException
     */
    private function scaleOptionFor(GradeItem $item, GradeEntryState $state, ?float $points, ?int $gradingScaleOptionId): ?GradingScaleOption
    {
        if ($item->type !== GradeItemType::Scale) {
            if ($gradingScaleOptionId !== null) {
                throw new InvalidValueException('Only a scale-based assessment can use a grade option.');
            }

            return null;
        }

        if ($points !== null) {
            throw new InvalidValueException('Choose a grade option instead of entering a number for a scale-based assessment.');
        }

        if (!$state->needsPoints()) {
            return null;
        }

        $item->loadMissing('gradingScale');

        if ($item->gradingScale === null) {
            throw new InvalidValueException('This scale-based assessment has no grading scale.');
        }

        if ($gradingScaleOptionId === null) {
            throw new InvalidValueException('Choose a grade option.');
        }

        $option = GradingScaleOption::query()
            ->whereBelongsTo($item->gradingScale)
            ->find($gradingScaleOptionId);

        if ($option === null) {
            throw new InvalidValueException('Choose an option from this assessment’s grading scale.');
        }

        return $option;
    }
}
