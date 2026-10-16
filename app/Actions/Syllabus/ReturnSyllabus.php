<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Syllabus;
use App\Models\User;
use App\Notifications\SyllabusWorkNotification;
use Illuminate\Support\Facades\DB;

/**
 * Send a submitted syllabus back to its author with the changes it needs.
 */
class ReturnSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function sendBack(Syllabus $syllabus, string $reviewNote, ?User $actor = null): Syllabus
    {
        $reviewNote = trim($reviewNote);

        if ($reviewNote === '') {
            throw new InvalidValueException('Say what needs to change before sending the syllabus back.');
        }

        $returned = DB::transaction(function () use ($syllabus, $reviewNote, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Submitted) {
                throw new InvalidValueException('Only a syllabus waiting for review can be sent back.');
            }

            $syllabus->update(['status' => SyllabusStatus::Draft, 'review_note' => $reviewNote]);
            $this->auditor->record(AuditAction::SyllabusReturned, $syllabus, ['revision' => $syllabus->revision, 'review_note' => $reviewNote], $actor);

            return $syllabus;
        });

        $author = $returned->submittedBy;

        if ($author !== null && $author->id !== $actor?->id) {
            $author->notify(new SyllabusWorkNotification(
                "{$returned->name} needs changes",
                ["A reviewer sent {$returned->name} back for changes:", $reviewNote, 'Change the draft, then send it for review again.'],
                'Edit the draft',
                route('syllabi.edit', $returned),
            ));
        }

        return $returned;
    }
}
