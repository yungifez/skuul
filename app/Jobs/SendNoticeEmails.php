<?php

namespace App\Jobs;

use App\Enums\NoticeRecipientState;
use App\Models\Notice;
use App\Models\NoticeNotificationPreference;
use App\Models\NoticeRecipient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Send one notice to everybody it was published to.
 *
 * Mail is slow and can fail, so it happens on the queue. A failure is written
 * to the recipient record instead of stopping the rest.
 */
class SendNoticeEmails implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    public function __construct(private int $noticeId) {}

    /**
     * Send the notice.
     */
    public function handle(): void
    {
        $notice = Notice::find($this->noticeId);

        if ($notice === null || !$notice->isPublished()) {
            return;
        }

        $recipients = NoticeRecipient::query()
            ->where('notice_id', $notice->id)
            ->with('user')
            ->get();
        $preferences = NoticeNotificationPreference::query()
            ->where('school_id', $notice->school_id)
            ->whereIn('user_id', $recipients->pluck('user_id'))
            ->pluck('email_enabled', 'user_id');

        foreach ($recipients as $recipient) {
            if (($preferences[$recipient->user_id] ?? true) === false) {
                continue;
            }

            $address = $recipient->user?->email;

            if ($address === null) {
                $recipient->state = NoticeRecipientState::Failed;
                $recipient->failure_reason = 'The person has no email address.';
                $recipient->save();

                continue;
            }

            try {
                Mail::raw(strip_tags((string) $notice->content), function (Message $message) use ($address, $notice): void {
                    $message->to($address)->subject($notice->title);
                });
            } catch (\Throwable $exception) {
                Log::warning('Notice email failed', ['notice_id' => $notice->id, 'user_id' => $recipient->user_id]);

                $recipient->state = NoticeRecipientState::Failed;
                $recipient->failure_reason = $exception->getMessage();
                $recipient->save();
            }
        }
    }
}
