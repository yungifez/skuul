<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicStructureStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JoinWaitlist
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Add a person to a full section's queue.
     *
     * @throws InvalidValueException
     */
    public function join(
        AcademicCycleSection $academicCycleSection,
        User $candidate,
        ?User $actor = null,
        int $priority = 0,
    ): AdmissionWaitlistEntry {
        return DB::transaction(function () use ($academicCycleSection, $candidate, $actor, $priority): AdmissionWaitlistEntry {
            $section = AcademicCycleSection::query()
                ->with('academicYear')
                ->lockForUpdate()
                ->findOrFail($academicCycleSection->getKey());

            $this->refuseWhatDoesNotFit($section, $candidate);

            $existing = AdmissionWaitlistEntry::query()
                ->where('school_id', $section->school_id)
                ->where('academic_cycle_section_id', $section->id)
                ->where('user_id', $candidate->id)
                ->open()
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            $position = ((int) AdmissionWaitlistEntry::query()
                ->where('school_id', $section->school_id)
                ->where('academic_cycle_section_id', $section->id)
                ->max('position')) + 1;

            $entry = AdmissionWaitlistEntry::create([
                'school_id' => $section->school_id,
                'academic_year_id' => $section->academic_year_id,
                'academic_cycle_section_id' => $section->id,
                'user_id' => $candidate->id,
                'created_by' => $actor?->id,
                'priority' => $priority,
                'position' => $position,
                'status' => AdmissionWaitlistStatus::Pending,
            ]);

            $this->auditor->record(
                AuditAction::AdmissionWaitlistJoined,
                $entry,
                ['academic_cycle_section_id' => $section->id, 'priority' => $priority],
                $actor,
                $section->school_id,
            );

            return $entry;
        });
    }

    /**
     * @throws InvalidValueException
     */
    private function refuseWhatDoesNotFit(AcademicCycleSection $section, User $candidate): void
    {
        if ($section->status !== AcademicStructureStatus::Active) {
            throw new InvalidValueException('Activate the cycle section before opening its admission queue.');
        }

        if ($section->academicYear->isClosed()) {
            throw new InvalidValueException('The academic cycle is closed. Reopen it before using its admission queue.');
        }

        if ($section->capacity === null) {
            throw new InvalidValueException('This section has no capacity limit, so it does not need an admission waitlist.');
        }

        if (!$candidate->belongsToSchool($section->school_id)) {
            throw new InvalidValueException('This candidate does not belong to the school.');
        }

        if (StudentRecord::query()
            ->where('school_id', $section->school_id)
            ->where('user_id', $candidate->id)
            ->where('status', EnrollmentStatus::Active)
            ->exists()) {
            throw new InvalidValueException('This candidate is already enrolled in the school.');
        }

        $occupied = StudentRecord::query()
            ->where('school_id', $section->school_id)
            ->where('academic_cycle_section_id', $section->id)
            ->where('status', EnrollmentStatus::Active)
            ->count();

        if ($occupied < $section->capacity) {
            throw new InvalidValueException('This section still has a place. Enrol the candidate instead of waitlisting them.');
        }
    }
}
