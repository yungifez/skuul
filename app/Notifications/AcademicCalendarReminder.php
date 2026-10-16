<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class AcademicCalendarReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $schoolName,
        private string $periodName,
        private string $kind,
        private Carbon $date,
    ) {}

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
            ->subject("{$this->schoolName}: {$this->periodName} calendar reminder")
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->message())
            ->action('Review academic calendar', route('academic-years.index'))
            ->line('Closing stays a deliberate staff confirmation. Finance continues independently of the academic calendar.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'school_name' => $this->schoolName,
            'period_name' => $this->periodName,
            'kind' => $this->kind,
            'date' => $this->date->toDateString(),
        ];
    }

    private function message(): string
    {
        return match ($this->kind) {
            'starts' => "{$this->periodName} at {$this->schoolName} starts on {$this->date->toFormattedDateString()}. Review the scheduled work before it opens.",
            'ends' => "{$this->periodName} at {$this->schoolName} ends on {$this->date->toFormattedDateString()}. Start the closure-readiness check now.",
            default => "{$this->periodName} at {$this->schoolName} ended on {$this->date->toFormattedDateString()} and is still open. Review its closure-readiness check.",
        };
    }
}
