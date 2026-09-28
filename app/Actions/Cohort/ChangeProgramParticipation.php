<?php

namespace App\Actions\Cohort;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\ParticipationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Program;
use App\Models\ProgramParticipation;
use App\Models\StudentRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Put a student into a programme and move their place through its states.
 *
 * Taking part never touches enrollment. A student who leaves a club is still
 * a student.
 */
class ChangeProgramParticipation
{
    public function __construct(private RecordAuditEvent $audit) {}

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
        ?User $actor = null,
    ): ProgramParticipation {
        if ($program->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('A student can only join a programme in their own school.');
        }

        return DB::transaction(function () use ($program, $enrollment, $startsOn, $staff, $schedule, $actor): ProgramParticipation {
            $program = Program::query()->lockForUpdate()->findOrFail($program->getKey());
            $this->refuseAClosedDoor($program, $enrollment);

            $running = $this->runningPlace($program, $enrollment);

            if ($running !== null) {
                return $running;
            }

            $participation = ProgramParticipation::create([
                'school_id' => $program->school_id,
                'program_id' => $program->id,
                'student_record_id' => $enrollment->id,
                'starts_on' => Carbon::parse($startsOn ?? now()),
                'schedule' => $schedule,
                'staff_id' => $staff?->id,
                'academic_year_id' => current_academic_year_id(),
            ]);

            $this->audit->record(AuditAction::ProgramParticipationChanged, $program, [
                'participation_id' => $participation->id,
                'student_record_id' => $enrollment->id,
                'status' => $participation->status->value,
            ], $actor);

            return $participation;
        });
    }

    /**
     * Move a place to another state.
     *
     * A caller that showed the place earlier passes the state it showed. When
     * somebody else moved the place since, nothing is written.
     *
     * @throws InvalidValueException when the state cannot follow the current one, or somebody else moved it first
     */
    public function changeStatus(
        ProgramParticipation $participation,
        ParticipationStatus $status,
        ?string $note = null,
        ?ParticipationStatus $seen = null,
        ?User $actor = null,
    ): ProgramParticipation {
        return DB::transaction(function () use ($participation, $status, $note, $seen, $actor): ProgramParticipation {
            $program = Program::query()->lockForUpdate()->findOrFail($participation->program_id);
            $participation = ProgramParticipation::query()->lockForUpdate()->findOrFail($participation->getKey());
            $current = $participation->status;

            if ($seen !== null && $current !== $seen) {
                throw new InvalidValueException("Somebody else already moved this place to {$current->label()}.");
            }

            if ($current === $status) {
                return $participation;
            }

            if (!$current->canMoveTo($status)) {
                throw new InvalidValueException("A place cannot move from {$current->label()} to {$status->label()}.");
            }

            if ($status->isRunning() && !$current->isRunning()) {
                $enrollment = StudentRecord::query()->findOrFail($participation->student_record_id);
                $this->refuseAClosedDoor($program, $enrollment);

                if ($this->runningPlace($program, $enrollment) !== null) {
                    throw new InvalidValueException('The learner already holds another place in this programme.');
                }
            }

            $participation->status = $status;
            $participation->note = $note ?? $participation->note;

            if (!$status->isRunning() && $participation->ends_on === null) {
                $participation->ends_on = $participation->starts_on !== null && $participation->starts_on->isFuture() ? $participation->starts_on : now();
            }

            if ($status->isRunning()) {
                $participation->ends_on = null;
            }

            $participation->save();

            $this->audit->record(AuditAction::ProgramParticipationChanged, $program, [
                'participation_id' => $participation->id,
                'student_record_id' => $participation->student_record_id,
                'from' => $current->value,
                'status' => $status->value,
            ], $actor);

            return $participation;
        });
    }

    /**
     * Withdraw the learner from every place they still hold at one campus.
     *
     * A learner who leaves the campus no longer attends its clubs, so their
     * places stop counting there. A learner who graduates finishes the places
     * they were taking part in instead.
     *
     * @return int the number of places ended
     */
    public function withdrawFromSchool(
        StudentRecord $enrollment,
        int $schoolId,
        string $note,
        ?User $actor = null,
        bool $finished = false,
    ): int {
        $places = ProgramParticipation::query()
            ->inSchool($schoolId)
            ->where('student_record_id', $enrollment->id)
            ->running()
            ->get();

        foreach ($places as $place) {
            $ending = $finished && $place->status === ParticipationStatus::Active ? ParticipationStatus::Completed : ParticipationStatus::Withdrawn;

            $this->changeStatus($place, $ending, $note, actor: $actor);
        }

        return $places->count();
    }

    /**
     * @throws InvalidValueException
     */
    private function refuseAClosedDoor(Program $program, StudentRecord $enrollment): void
    {
        if ($program->school_id !== $enrollment->school_id) {
            throw new InvalidValueException('A student can only join a programme in their own school.');
        }

        if (!$program->is_active) {
            throw new InvalidValueException('This programme is closed.');
        }

        if ($enrollment->status !== EnrollmentStatus::Active) {
            throw new InvalidValueException('A programme needs an active enrollment.');
        }
    }

    private function runningPlace(Program $program, StudentRecord $enrollment): ?ProgramParticipation
    {
        return ProgramParticipation::query()
            ->where('program_id', $program->id)
            ->where('student_record_id', $enrollment->id)
            ->running()
            ->first();
    }
}
