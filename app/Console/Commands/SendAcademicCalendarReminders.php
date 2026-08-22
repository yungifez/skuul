<?php

namespace App\Console\Commands;

use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Notifications\AcademicCalendarReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SendAcademicCalendarReminders extends Command
{
    protected $signature = 'skuul:send-academic-calendar-reminders {--dry-run : List reminders without sending them}';

    protected $description = 'Send staff calendar reminders before periods start or need closure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sent = 0;

        foreach (School::query()->with(['calendarTemplate', 'organization.calendarTemplates', 'users'])->get() as $school) {
            $template = $school->calendarTemplate
                ?? $school->organization?->calendarTemplates->firstWhere('is_default', true);

            if ($template === null || $template->remind_days_before === 0) {
                continue;
            }

            school_context()->set($school, remember: false);

            foreach ($this->dueReminders($school, (int) $template->remind_days_before) as $reminder) {
                $recipients = $school->users->filter(fn (User $user): bool => $user->can('close academic period') || $user->can('update academic year'));

                if ($recipients->isEmpty()) {
                    continue;
                }

                $message = new AcademicCalendarReminder(
                    $school->name,
                    $reminder['period']->displayName ?? $reminder['period']->name,
                    $reminder['kind'],
                    $reminder['date'],
                );

                if ($this->option('dry-run')) {
                    $this->line("would remind {$recipients->count()} staff about {$reminder['period']->name}");
                    $sent += $recipients->count();

                    continue;
                }

                if (!$this->claim($reminder['period'], $reminder['kind'])) {
                    continue;
                }

                $recipients->each(fn (User $user) => $user->notify($message));
                $sent += $recipients->count();
            }
        }

        $this->info($this->option('dry-run') ? "{$sent} reminder(s) would be sent." : "{$sent} reminder(s) sent.");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{period: AcademicYear|AcademicPeriod, kind: string, date: Carbon}>
     */
    private function dueReminders(School $school, int $leadDays): array
    {
        $today = now()->startOfDay();
        $reminders = [];

        foreach ($this->periodsStartingOn($school, $today->copy()->addDays($leadDays)) as $period) {
            $reminders[] = ['period' => $period, 'kind' => 'starts', 'date' => $period->starts_on];
        }

        foreach ($this->periodsEndingOn($school, $today->copy()->addDays($leadDays)) as $period) {
            $reminders[] = ['period' => $period, 'kind' => 'ends', 'date' => $period->ends_on];
        }

        foreach ($this->overduePeriods($school, $today) as $period) {
            $reminders[] = ['period' => $period, 'kind' => 'overdue', 'date' => $period->ends_on];
        }

        return $reminders;
    }

    /** @return Collection<int, AcademicYear|AcademicPeriod> */
    private function periodsStartingOn(School $school, Carbon $date): Collection
    {
        return AcademicYear::query()->where('school_id', $school->id)->where('status', AcademicPeriodStatus::Scheduled)->whereDate('starts_on', $date)->get()
            ->concat(AcademicPeriod::query()->where('school_id', $school->id)->where('status', AcademicPeriodStatus::Scheduled)->whereDate('starts_on', $date)->get());
    }

    /** @return Collection<int, AcademicYear|AcademicPeriod> */
    private function periodsEndingOn(School $school, Carbon $date): Collection
    {
        return AcademicYear::query()->where('school_id', $school->id)->whereIn('status', [AcademicPeriodStatus::Open, AcademicPeriodStatus::Closing])->whereDate('ends_on', $date)->get()
            ->concat(AcademicPeriod::query()->where('school_id', $school->id)->whereIn('status', [AcademicPeriodStatus::Open, AcademicPeriodStatus::Closing])->whereDate('ends_on', $date)->get());
    }

    /** @return Collection<int, AcademicYear|AcademicPeriod> */
    private function overduePeriods(School $school, Carbon $today): Collection
    {
        return AcademicYear::query()->where('school_id', $school->id)->whereIn('status', [AcademicPeriodStatus::Open, AcademicPeriodStatus::Closing])->whereDate('ends_on', '<', $today)->get()
            ->concat(AcademicPeriod::query()->where('school_id', $school->id)->whereIn('status', [AcademicPeriodStatus::Open, AcademicPeriodStatus::Closing])->whereDate('ends_on', '<', $today)->get());
    }

    private function claim(AcademicYear|AcademicPeriod $period, string $kind): bool
    {
        return Cache::add(
            "academic-calendar-reminder:{$period->getMorphClass()}:{$period->id}:{$kind}:{$period->starts_on?->toDateString()}:{$period->ends_on?->toDateString()}",
            true,
            now()->addYear(),
        );
    }
}
