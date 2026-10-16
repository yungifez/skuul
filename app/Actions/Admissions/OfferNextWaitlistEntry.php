<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Academic\SectionSeats;
use Illuminate\Support\Facades\DB;

class OfferNextWaitlistEntry
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private SectionSeats $seats,
    ) {}

    public function offer(AcademicCycleSection $academicCycleSection, ?User $actor = null): ?AdmissionWaitlistEntry
    {
        return DB::transaction(function () use ($academicCycleSection, $actor): ?AdmissionWaitlistEntry {
            $section = AcademicCycleSection::query()->lockForUpdate()->findOrFail($academicCycleSection->getKey());

            // An open offer holds its seat, so one free seat is never offered
            // to two families. A section whose limit was lifted has room for
            // everybody still waiting.
            if ($this->seats->isFull($section)) {
                return null;
            }

            $entry = $this->nextCandidateNotEnrolledHere($section, $actor);

            if ($entry === null) {
                return null;
            }

            $entry->update([
                'status' => AdmissionWaitlistStatus::Offered,
                'offered_at' => now(),
                'offered_by' => $actor?->id,
            ]);

            $this->auditor->record(
                AuditAction::AdmissionWaitlistOffered,
                $entry,
                ['academic_cycle_section_id' => $section->id],
                $actor,
                $section->school_id,
            );

            return $entry->refresh();
        });
    }

    /**
     * Withdraw candidates already enrolled here before offering the next seat.
     * Candidates enrolled at another school remain eligible for a move or transfer.
     */
    private function nextCandidateNotEnrolledHere(AcademicCycleSection $section, ?User $actor): ?AdmissionWaitlistEntry
    {
        $waiting = AdmissionWaitlistEntry::query()
            ->where('school_id', $section->school_id)
            ->where('academic_cycle_section_id', $section->id)
            ->where('status', AdmissionWaitlistStatus::Pending)
            ->orderByDesc('priority')
            ->orderBy('position')
            ->lockForUpdate()
            ->get();

        foreach ($waiting as $entry) {
            $enrolledHere = StudentRecord::query()
                ->where('user_id', $entry->user_id)
                ->where('school_id', $section->school_id)
                ->enrolled()
                ->exists();

            if (!$enrolledHere) {
                return $entry;
            }

            $reason = 'Enrolled in this school while waiting.';

            $entry->update([
                'status' => AdmissionWaitlistStatus::Withdrawn,
                'decided_at' => now(),
                'decided_by' => $actor?->id,
                'decision_reason' => $reason,
            ]);

            $this->auditor->record(
                AuditAction::AdmissionWaitlistDeclined,
                $entry,
                ['reason' => $reason],
                $actor,
                $section->school_id,
            );
        }

        return null;
    }
}
