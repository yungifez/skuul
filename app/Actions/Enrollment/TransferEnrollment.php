<?php

namespace App\Actions\Enrollment;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\MyClass;
use App\Models\School;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\DB;

/**
 * Move a student to another school without losing the first school's records.
 *
 * A transfer closes the enrollment in the old school and opens a new one in
 * the new school. The old enrollment keeps its history and stays readable; the
 * new enrollment names the one it continues.
 */
class TransferEnrollment
{
    public function __construct(
        private ChangeEnrollmentStatus $changeStatus,
        private ChangeEnrollmentPlacement $changePlacement,
        private StudentService $studentService,
        private RecordAuditEvent $auditor,
    ) {
    }

    /**
     * Transfer the enrollment to the destination school.
     *
     * @throws InvalidValueException when the destination is the same school
     */
    public function transfer(
        StudentRecord $enrollment,
        School $destination,
        ?MyClass $class = null,
        ?Section $section = null,
        ?User $actor = null,
        ?string $reason = null,
    ): StudentRecord {
        if ($enrollment->school_id === $destination->id) {
            throw new InvalidValueException('The student is already enrolled in that school.');
        }

        if ($enrollment->status->isClosed() && $enrollment->status !== EnrollmentStatus::Transferred) {
            throw new InvalidValueException('This enrollment is closed and cannot be transferred.');
        }

        return DB::transaction(function () use ($enrollment, $destination, $class, $section, $actor, $reason): StudentRecord {
            $this->changeStatus->change($enrollment, EnrollmentStatus::Transferred, $actor, $reason);

            // Only one enrollment leads the screens, and it is the new one.
            $enrollment->is_primary = false;
            $enrollment->save();

            $transferred = StudentRecord::create([
                'user_id'             => $enrollment->user_id,
                'school_id'           => $destination->id,
                'status'              => EnrollmentStatus::Active,
                'is_primary'          => true,
                'transferred_from_id' => $enrollment->id,
                'admission_number'    => $this->studentService->generateAdmissionNumber($destination->id),
                'admission_date'      => now()->toDateString(),
            ]);

            if ($class !== null) {
                $this->changePlacement->place(
                    enrollment: $transferred,
                    class: $class,
                    section: $section,
                    academicYear: $destination->academicYear,
                    actor: $actor,
                    reason: $reason,
                );
            }

            $this->auditor->record(
                AuditAction::EnrollmentTransferred,
                $transferred,
                [
                    'from_school_id'     => $enrollment->school_id,
                    'to_school_id'       => $destination->id,
                    'from_enrollment_id' => $enrollment->id,
                    'reason'             => $reason,
                ],
                $actor,
                $destination,
            );

            return $transferred;
        });
    }
}
