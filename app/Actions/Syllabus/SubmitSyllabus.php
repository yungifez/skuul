<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Send a syllabus draft to a reviewer, or take it back.
 */
class SubmitSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function submit(Syllabus $syllabus, ?User $actor = null): Syllabus
    {
        return DB::transaction(function () use ($syllabus, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Draft) {
                throw new InvalidValueException('Only a draft syllabus can be sent for review.');
            }

            if (!$syllabus->topics()->exists()) {
                throw new InvalidValueException('Add at least one topic before sending the syllabus for review.');
            }

            $syllabus->update([
                'status' => SyllabusStatus::Submitted,
                'submitted_at' => now(),
                'submitted_by' => ($actor ?? auth()->user())?->id,
            ]);
            $this->auditor->record(AuditAction::SyllabusSubmitted, $syllabus, ['revision' => $syllabus->revision], $actor);

            return $syllabus;
        });
    }

    public function withdraw(Syllabus $syllabus): Syllabus
    {
        return DB::transaction(function () use ($syllabus): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Submitted) {
                throw new InvalidValueException('This syllabus is not waiting for review.');
            }

            $syllabus->update(['status' => SyllabusStatus::Draft]);

            return $syllabus;
        });
    }
}
