<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Models\AcademicCycleSection;
use App\Models\AdmissionWaitlistEntry;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OfferNextWaitlistEntry
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function offer(AcademicCycleSection $academicCycleSection, ?User $actor = null): ?AdmissionWaitlistEntry
    {
        return DB::transaction(function () use ($academicCycleSection, $actor): ?AdmissionWaitlistEntry {
            $section = AcademicCycleSection::query()->lockForUpdate()->findOrFail($academicCycleSection->getKey());

            if ($section->capacity === null) {
                return null;
            }

            $occupied = StudentRecord::query()
                ->where('school_id', $section->school_id)
                ->where('academic_cycle_section_id', $section->id)
                ->where('status', 'active')
                ->count();

            if ($occupied >= $section->capacity) {
                return null;
            }

            $entry = AdmissionWaitlistEntry::query()
                ->where('school_id', $section->school_id)
                ->where('academic_cycle_section_id', $section->id)
                ->where('status', AdmissionWaitlistStatus::Pending)
                ->orderByDesc('priority')
                ->orderBy('position')
                ->lockForUpdate()
                ->first();

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
}
