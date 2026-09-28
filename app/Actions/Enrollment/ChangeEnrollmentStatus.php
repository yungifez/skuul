<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Cohort\ChangeCohortMembership;
use App\Actions\Cohort\ChangeProgramParticipation;
use App\Actions\Library\CloseReservation;
use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\EnrollmentStatusChange;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Academic\SectionSeats;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Move one enrollment to another state and record why.
 *
 * The state lives on the enrollment. The reason, the actor, and the date live
 * in an append-only history, so nothing is lost when a student graduates,
 * leaves, or returns. Repeating the same request changes nothing and adds no
 * second history record, so a retry is always safe.
 */
class ChangeEnrollmentStatus
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private AssignBoardingPlace $boarding,
        private ChangeProgramParticipation $programmes,
        private ChangeCohortMembership $cohorts,
        private RequestCampusMove $campusMoves,
        private CloseReservation $reservations,
        private ManageSupportPlan $supportPlans,
        private SectionSeats $seats,
        private ChangeEnrollmentPlacement $placement,
    ) {}

    /**
     * Move the enrollment to the given state.
     *
     * @param  AcademicCycleSection|null  $into  for a learner taken back, the section they join; their old section's seats then do not count
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function change(
        StudentRecord $enrollment,
        EnrollmentStatus $status,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
        ?AcademicCycleSection $into = null,
    ): StudentRecord {
        return DB::transaction(function () use ($enrollment, $status, $actor, $reason, $effectiveOn, $into): StudentRecord {
            // Re-read the row under a lock. This makes retries idempotent even
            // when two requests attempt to change the same enrollment at once.
            $enrollment = StudentRecord::query()
                ->lockForUpdate()
                ->findOrFail($enrollment->getKey());
            $current = $enrollment->status;

            // A repeated request is not an error. Nothing changed, so record nothing.
            if ($current === $status) {
                return $enrollment;
            }

            if (!$current->canMoveTo($status)) {
                throw new InvalidValueException(
                    "An enrollment cannot move from {$current->value} to {$status->value}."
                );
            }

            if ($into !== null && !$current->isClosed()) {
                throw new InvalidValueException('Only a learner who left can be taken back into a section.');
            }

            if ($current->isClosed()) {
                $this->refuseALearnerWhoNowAttendsElsewhere($enrollment);

                if ($into === null) {
                    $this->refuseASeatThatIsNoLongerFree($enrollment);
                }
            }

            $enrollment->status = $status;
            $enrollment->save();

            EnrollmentStatusChange::create([
                'student_record_id' => $enrollment->id,
                'from_status' => $current,
                'to_status' => $status,
                'effective_on' => $effectiveOn ?? now(),
                'changed_by' => $actor?->id,
                'reason' => $reason,
            ]);

            // Placing checks the new section's seats, under its lock.
            if ($into !== null) {
                $enrollment = $this->placement->place(
                    enrollment: $enrollment,
                    academicCycleSection: $into,
                    actor: $actor,
                    reason: $reason,
                    effectiveOn: $effectiveOn,
                );
            }

            if ($status->isClosed()) {
                $this->boarding->release($enrollment, "Enrollment closed: {$status->label()}", $actor, $effectiveOn);
                $this->programmes->withdrawFromSchool($enrollment, $enrollment->school_id, "Enrollment closed: {$status->label()}", $actor, finished: $status === EnrollmentStatus::Graduated);
                $this->supportPlans->closeAtSchool($enrollment, $enrollment->school_id, "Enrollment closed: {$status->label()}", $actor);

                if ($enrollment->user !== null) {
                    $this->reservations->cancelEveryReservation($enrollment->user, $enrollment->school_id, $actor);
                }

                // A closed enrollment can never move, so a move still
                // waiting would only fail when somebody tried to approve it.
                $waiting = $this->campusMoves->openRequestFor($enrollment);

                if ($waiting !== null) {
                    $this->campusMoves->cancel($waiting, $actor, "Enrollment closed: {$status->label()}");
                }

                // A graduate stays in the class they graduated with.
                if ($status !== EnrollmentStatus::Graduated) {
                    $this->cohorts->leaveSchool($enrollment, $enrollment->school_id, $effectiveOn, $actor);
                }
            }

            $this->auditor->record(
                AuditAction::EnrollmentStatusChanged,
                $enrollment,
                ['from' => $current->value, 'to' => $status->value, 'reason' => $reason],
                $actor,
            );

            return $enrollment;
        });
    }

    /**
     * Refuse to reopen an enrollment while the learner is enrolled somewhere else.
     *
     * A graduate or a leaver can start again at another campus, or be taken in
     * again under a new enrollment. Reopening the old one would then count them
     * twice, and two schools would bill them.
     *
     * @throws InvalidValueException when another enrollment of the learner is open
     */
    private function refuseALearnerWhoNowAttendsElsewhere(StudentRecord $enrollment): void
    {
        $open = StudentRecord::query()
            ->where('user_id', $enrollment->user_id)
            ->whereKeyNot($enrollment->getKey())
            ->enrolled()
            ->with('school:id,name')
            ->first();

        if ($open !== null) {
            throw new InvalidValueException("This learner now attends {$open->school?->name}. Ask that school to move or transfer them.");
        }
    }

    /**
     * Refuse to reopen an enrollment into a section that filled up since.
     *
     * A closed enrollment gave up its seat. Taking it back must not push the
     * section past the size the school set.
     *
     * @throws InvalidValueException when the learner's section is full
     */
    private function refuseASeatThatIsNoLongerFree(StudentRecord $enrollment): void
    {
        if ($enrollment->academic_cycle_section_id === null) {
            return;
        }

        $section = AcademicCycleSection::query()
            ->lockForUpdate()
            ->find($enrollment->academic_cycle_section_id);

        if ($section !== null && $this->seats->isFull($section, except: $enrollment)) {
            throw new InvalidValueException("Their section is full at {$section->capacity} learners. Free a seat or raise its size first.");
        }
    }

    /**
     * Record that the student finished the program.
     */
    public function graduate(StudentRecord $enrollment, ?User $actor = null, ?string $reason = null, ?CarbonInterface $effectiveOn = null): StudentRecord
    {
        return $this->change($enrollment, EnrollmentStatus::Graduated, $actor, $reason, $effectiveOn);
    }

    /**
     * Return the enrollment to attendance.
     *
     * Use this to correct a graduation, end a suspension, or take a student
     * back after they withdrew.
     */
    public function returnToAttendance(StudentRecord $enrollment, ?User $actor = null, ?string $reason = null, ?CarbonInterface $effectiveOn = null): StudentRecord
    {
        return $this->change($enrollment, EnrollmentStatus::Active, $actor, $reason, $effectiveOn);
    }
}
