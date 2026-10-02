<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Invite a provisioned person to set a password and sign in.
 *
 * Sending mail is slow, so the message goes to the queue. The screen that
 * provisions the account does not wait for the mail server.
 */
class AccountInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token  the plain, one-time invitation token
     * @param  Carbon  $expiresAt  the time the link stops working
     */
    public function __construct(private string $token, private Carbon $expiresAt) {}

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

        return (new MailMessage)
            ->subject('Set up your '.config('app.name').' account')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('An administrator created an account for you on '.config('app.name').'.')
            ->line('Use the button below to set your password and sign in.')
            ->action('Set my password', $url)
            ->line('This link works one time only. It expires on '.$this->expiresAt->toDayDateTimeString().'.')
            ->line('If you did not expect this email, you can ignore it.');
    }
}
