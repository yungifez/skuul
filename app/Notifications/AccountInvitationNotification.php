<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Invite a provisioned person to set a password and sign in.
 */
class AccountInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $token     the plain, one-time invitation token
     * @param Carbon $expiresAt the time the link stops working
     */
    public function __construct(private string $token, private Carbon $expiresAt)
    {
    }

    /**
     * Get the delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail message.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('invitations.show', ['token' => $this->token]);

        return (new MailMessage())
            ->subject('Set up your '.config('app.name').' account')
            ->greeting('Hello '.$notifiable->firstName().',')
            ->line('An administrator created an account for you on '.config('app.name').'.')
            ->line('Use the button below to set your password and sign in.')
            ->action('Set my password', $url)
            ->line('This link works one time only. It expires on '.$this->expiresAt->toDayDateTimeString().'.')
            ->line('If you did not expect this email, you can ignore it.');
    }
}
