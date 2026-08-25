<?php

namespace App\Actions\Admissions;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AdmissionWaitlistEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeclineWaitlistEntry
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * @throws InvalidValueException
     */
    public function decline(AdmissionWaitlistEntry $entry, ?User $actor = null, ?string $reason = null): AdmissionWaitlistEntry
    {
        return DB::transaction(function () use ($entry, $actor, $reason): AdmissionWaitlistEntry {
            $entry = AdmissionWaitlistEntry::query()->lockForUpdate()->findOrFail($entry->getKey());

            if (!$entry->isOpen()) {
                throw new InvalidValueException('This admission waitlist entry has already been decided.');
            }

            $entry->update([
                'status' => AdmissionWaitlistStatus::Declined,
                'decided_at' => now(),
                'decided_by' => $actor?->id,
                'decision_reason' => $reason,
            ]);

            $this->auditor->record(
                AuditAction::AdmissionWaitlistDeclined,
                $entry,
                ['reason' => $reason],
                $actor,
                $entry->school_id,
            );

            return $entry->refresh();
        });
    }
}
