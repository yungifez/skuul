<?php

namespace App\Actions\Notice;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\NoticeStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Start a corrected draft without overwriting a published family message.
 */
class ReviseNotice
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Copy a published notice into the next revision.
     *
     * @param  array{title?: string, content?: string, audience?: array<string, mixed>|null, send_email?: bool, start_date?: string, stop_date?: string, attachment?: string|null}  $changes
     *
     * @throws InvalidValueException when the notice is not a published version
     */
    public function revise(Notice $notice, array $changes = [], ?User $actor = null): Notice
    {
        return DB::transaction(function () use ($notice, $changes, $actor): Notice {
            $notice = Notice::query()->lockForUpdate()->findOrFail($notice->id);

            if ($notice->status !== NoticeStatus::Published) {
                throw new InvalidValueException('Only a published notice can be revised.');
            }

            $revision = Notice::create([
                'title' => $changes['title'] ?? $notice->title,
                'content' => $changes['content'] ?? $notice->content,
                'attachment' => array_key_exists('attachment', $changes) ? $changes['attachment'] : $notice->attachment,
                'start_date' => $changes['start_date'] ?? $notice->start_date,
                'stop_date' => $changes['stop_date'] ?? $notice->stop_date,
                'school_id' => $notice->school_id,
                'status' => NoticeStatus::Draft,
                'audience' => $changes['audience'] ?? $notice->audience,
                'send_email' => $changes['send_email'] ?? $notice->send_email,
                'revision' => $notice->revision + 1,
                'revision_of_id' => $notice->id,
                'active' => false,
            ]);

            $this->auditor->record(
                AuditAction::NoticeRevised,
                $revision,
                ['revision' => $revision->revision, 'revision_of_id' => $notice->id],
                $actor,
            );

            return $revision;
        });
    }
}
