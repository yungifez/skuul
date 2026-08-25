<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
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

            if (StudentRecord::query()
                ->where('school_id', $entry->school_id)
                ->where('user_id', $entry->user_id)
                ->exists()) {
                throw new InvalidValueException('This candidate already has an enrollment in the school.');
            }

            $candidate = $entry->candidate()->firstOrFail();
            $candidate->assignRole(Role::Student);

            $enrollment = StudentRecord::create([
                'school_id' => $entry->school_id,
                'user_id' => $entry->user_id,
                'admission_number' => $this->students->generateAdmissionNumber($entry->school_id),
                'admission_date' => now(),
            ]);

            $enrollment = $this->place->place(
                enrollment: $enrollment,
                academicCycleSection: $entry->academicCycleSection,
                actor: $actor,
                reason: 'Admission waitlist accepted',
            );

            $entry->update([
                'status' => AdmissionWaitlistStatus::Placed,
                'decided_at' => now(),
                'decided_by' => $actor?->id,
            ]);

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
}
