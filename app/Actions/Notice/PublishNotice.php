<?php

namespace App\Actions\Notice;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\NoticeRecipientState;
use App\Enums\NoticeStatus;
use App\Exceptions\InvalidValueException;
use App\Jobs\SendNoticeEmails;
use App\Models\Notice;
use App\Models\NoticeRecipient;
use App\Models\User;
use App\Services\Notice\NoticeAudience;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Put a notice on the board and write down who it went to.
 *
 * Publication is the moment the audience is fixed. A recipient record per
 * person is what lets the school answer later whether a family was told and
 * whether they read it.
 */
class PublishNotice
{
    public function __construct(
        private NoticeAudience $audience,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Publish the notice now.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function publish(Notice $notice, ?User $actor = null): Notice
    {
        return DB::transaction(function () use ($notice, $actor): Notice {
            $notice = Notice::query()->lockForUpdate()->findOrFail($notice->id);

            if ($notice->status === NoticeStatus::Published) {
                return $notice;
            }

            if (!$notice->status->canMoveTo(NoticeStatus::Published)) {
                throw new InvalidValueException('This notice cannot be published from its current state.');
            }

            $previousRevision = $notice->revision_of_id === null
                ? null
                : Notice::query()->lockForUpdate()->findOrFail($notice->revision_of_id);

            if ($previousRevision !== null && $previousRevision->status !== NoticeStatus::Published) {
                throw new InvalidValueException('This notice revision has already been superseded.');
            }

            $people = $this->audience->resolve($notice);

            foreach ($people as $person) {
                NoticeRecipient::firstOrCreate(
                    ['notice_id' => $notice->id, 'user_id' => $person->id],
                    ['state' => NoticeRecipientState::Delivered, 'delivered_at' => now()],
                );
            }

            $notice->status = NoticeStatus::Published;
            $notice->published_at = now();
            $notice->published_by = $actor === null ? auth()->id() : $actor->id;
            $notice->active = true;
            $notice->save();

            if ($previousRevision !== null) {
                $previousRevision->status = NoticeStatus::Superseded;
                $previousRevision->active = false;
                $previousRevision->save();
            }

            // Email is slow and optional, so it leaves the request.
            if ($notice->send_email) {
                SendNoticeEmails::dispatch($notice->id);
            }

            $this->auditor->record(
                AuditAction::NoticePublished,
                $notice,
                ['recipients' => $people->count(), 'send_email' => $notice->send_email],
                $actor,
            );

            return $notice;
        });
    }

    /**
     * Hold the notice until the given moment.
     */
    public function schedule(Notice $notice, CarbonInterface $when, ?User $actor = null): Notice
    {
        if (!$notice->status->canMoveTo(NoticeStatus::Scheduled)) {
            throw new InvalidValueException('This notice cannot be scheduled from its current state.');
        }

        $notice->status = NoticeStatus::Scheduled;
        $notice->scheduled_for = Carbon::parse($when);
        $notice->save();

        $this->auditor->record(
            AuditAction::NoticeScheduled,
            $notice,
            ['scheduled_for' => $notice->scheduled_for?->toIso8601String()],
            $actor,
        );

        return $notice;
    }

    /**
     * Take the notice off the board because its last day has passed.
     */
    public function expire(Notice $notice, ?User $actor = null): Notice
    {
        if (!$notice->status->canMoveTo(NoticeStatus::Expired)) {
            return $notice;
        }

        $notice->status = NoticeStatus::Expired;
        $notice->active = false;
        $notice->save();

        $this->auditor->record(AuditAction::NoticeExpired, $notice, [], $actor);

        return $notice;
    }
}
