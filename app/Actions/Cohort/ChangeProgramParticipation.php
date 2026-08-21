<?php

namespace App\Actions\Cohort;

use App\Enums\EnrollmentStatus;
use App\Enums\ParticipationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Program;
use App\Models\ProgramParticipation;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Put a student into a programme and move their place through its states.
 *
 * Taking part never touches enrollment. A student who leaves a club is still
 * a student.
 */
class ChangeProgramParticipation
{
    /**
     * Give a student a place.
     *
     * @throws InvalidValueException when the programme is closed, in another school, or the enrollment is closed
     */
    public function join(
        Program $program,
        StudentRecord $enrollment,
        CarbonInterface|string|null $startsOn = null,
        ?User $staff = null,
        ?string $schedule = null,
    ): ProgramParticipation {
        if ($program->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('A student can only join a programme in their own school.');
        }

        if (!$program->is_active) {
            throw new InvalidValueException('This programme is closed.');
        }

        if ($enrollment->status !== EnrollmentStatus::Active) {
            throw new InvalidValueException('A programme needs an active enrollment.');
        }

        $running = ProgramParticipation::query()
            ->where('program_id', $program->id)
            ->where('student_record_id', $enrollment->id)
            ->running()
            ->first();

        if ($running !== null) {
            return $running;
        }

        return ProgramParticipation::create([
            'school_id'         => $program->school_id,
            'program_id'        => $program->id,
            'student_record_id' => $enrollment->id,
            'starts_on'         => Carbon::parse($startsOn ?? now()),
            'schedule'          => $schedule,
            'staff_id'          => $staff?->id,
            'academic_year_id'  => current_academic_year_id(),
        ]);
    }

    /**
     * Move a place to another state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(
        ProgramParticipation $participation,
        ParticipationStatus $status,
        ?string $note = null,
    ): ProgramParticipation {
        $current = $participation->status;

        if ($current === $status) {
            return $participation;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("A place cannot move from {$current->value} to {$status->value}.");
        }

        $participation->status = $status;
        $participation->note = $note ?? $participation->note;

        if (!$status->isRunning() && $participation->ends_on === null) {
            $participation->ends_on = now();
        }

        if ($status->isRunning()) {
            $participation->ends_on = null;
        }

        $participation->save();

        return $participation;
    }
}
