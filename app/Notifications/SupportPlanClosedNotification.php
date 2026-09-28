<?php

namespace App\Notifications;

use App\Models\SupportPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tell the person who looks after a support plan that it closed without them.
 *
 * The mail names no learner, no need, and no new campus. It only links to the
 * plan, which the reader opens after signing in, so nothing private sits in a
 * mailbox.
 */
class SupportPlanClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportPlan $plan)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A support plan you look after was closed')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A support plan you look after was closed because the learner left your campus.')
            ->line('Sign in to see the plan and hand over anything still open.')
            ->action('Open the plan', route('support-plans.show', $this->plan->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'support_plan_id' => $this->plan->id,
        ];
    }
}
