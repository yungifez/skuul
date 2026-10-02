<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AdmissionWaitlistEntry;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Support\Facades\DB;

class AcceptWaitlistEntry
{
    public function __construct(
        private ChangeEnrollmentPlacement $place,
        private StudentService $students,
        private RecordAuditEvent $auditor,
        private ChangeEnrollmentStatus $status,
    ) {}

    /**
     * Accept the offer and create the student's enrollment.
     *
     * @throws InvalidValueException
     */
    public function accept(AdmissionWaitlistEntry $entry, ?User $actor = null): StudentRecord
    {
        return DB::transaction(function () use ($entry, $actor): StudentRecord {
            $entry = AdmissionWaitlistEntry::query()
                ->with('academicCycleSection')
                ->lockForUpdate()
                ->findOrFail($entry->getKey());

            if ($entry->status !== AdmissionWaitlistStatus::Offered) {
                throw new InvalidValueException('Only an offered admission place can be accepted.');
            }

            $earlier = StudentRecord::query()
                ->where('school_id', $entry->school_id)
                ->where('user_id', $entry->user_id)
                ->first();

            if ($earlier !== null && !$earlier->status->isClosed()) {
                throw new InvalidValueException('This candidate already attends the school.');
            }

            if ($earlier !== null && !$earlier->status->canMoveTo(EnrollmentStatus::Active)) {
                throw new InvalidValueException("This candidate's earlier enrollment here is {$earlier->status->label()}, so it cannot be opened again.");
            }

            // The admission form refuses the same thing. Accepting a place
            // must not give one learner two schools to attend and pay.
            $attendingElsewhere = StudentRecord::query()
                ->where('user_id', $entry->user_id)
                ->where('school_id', '!=', $entry->school_id)
                ->enrolled()
                ->with('school:id,name,organization_id')
                ->first();

            if ($attendingElsewhere !== null) {
                throw new InvalidValueException($attendingElsewhere->school?->organization_id === $entry->school?->organization_id
                    ? "This candidate attends {$attendingElsewhere->school->name}. Ask that school to move or transfer them."
                    : 'This candidate is enrolled at another school. Ask that school to move or transfer them.');
            }

            $candidate = $entry->candidate()->firstOrFail();

            if ($candidate->worksAsStaff()) {
                throw new InvalidValueException("{$candidate->name} works as staff. A member of staff cannot be admitted as a learner.");
            }

            $candidate->assignRole(Role::Student);

            // The offer becomes the placement. Closing it first lets its own
            // seat take the learner; the transaction undoes both if placing fails.
            $entry->update([
                'status' => AdmissionWaitlistStatus::Placed,
                'decided_at' => now(),
                'decided_by' => $actor?->id,
            ]);

            // A learner who left and comes back keeps their one enrollment
            // here, with its history and admission number.
            $enrollment = $earlier === null
                ? $this->placeANewLearner($entry, $actor)
                : $this->status->change(
                    enrollment: $earlier,
                    status: EnrollmentStatus::Active,
                    actor: $actor,
                    reason: 'Admission waitlist accepted',
                    into: $entry->academicCycleSection,
                );

            $this->auditor->record(
                AuditAction::AdmissionWaitlistPlaced,
                $entry,
                ['student_record_id' => $enrollment->id],
                $actor,
                $entry->school_id,
            );

            return $enrollment;
        });
    }

    /**
     * Create the enrollment of a learner new to the school and seat them.
     */
    private function placeANewLearner(AdmissionWaitlistEntry $entry, ?User $actor): StudentRecord
    {
        $enrollment = StudentRecord::create([
            'school_id' => $entry->school_id,
            'user_id' => $entry->user_id,
            'admission_number' => $this->students->generateAdmissionNumber($entry->school_id),
            'admission_date' => school_today($entry->school_id),
        ]);

        return $this->place->place(
            enrollment: $enrollment,
            academicCycleSection: $entry->academicCycleSection,
            actor: $actor,
            reason: 'Admission waitlist accepted',
        );
    }
}
