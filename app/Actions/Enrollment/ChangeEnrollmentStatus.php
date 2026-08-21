<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\EnrollmentStatusChange;
use App\Models\StudentRecord;
use App\Models\User;
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
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Move the enrollment to the given state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function change(
        StudentRecord $enrollment,
        EnrollmentStatus $status,
        ?User $actor = null,
        ?string $reason = null,
        ?CarbonInterface $effectiveOn = null,
    ): StudentRecord {
        return DB::transaction(function () use ($enrollment, $status, $actor, $reason, $effectiveOn): StudentRecord {
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
