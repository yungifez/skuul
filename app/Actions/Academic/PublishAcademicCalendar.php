<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PublishAcademicCalendar
{
    public function __construct(
        private ChangeAcademicPeriodStatus $lifecycle,
        private RecordAuditEvent $auditor,
    ) {}

    public function publish(AcademicYear $academicYear, ?User $actor = null): AcademicYear
    {
        if ($academicYear->status !== AcademicPeriodStatus::Draft) {
            throw new InvalidValueException('Only a draft calendar can be published.');
        }

        if ($academicYear->starts_on === null || $academicYear->ends_on === null) {
            throw new InvalidValueException('Give the school calendar a start and end date before publishing it.');
        }

        if ($academicYear->topLevelPeriods()->doesntExist()) {
            throw new InvalidValueException('Add at least one reporting period before publishing the school calendar.');
        }

        return DB::transaction(function () use ($academicYear, $actor): AcademicYear {
            $this->lifecycle->change($academicYear, $this->statusFor($academicYear->starts_on, $academicYear->ends_on), $actor);

            foreach ($academicYear->topLevelPeriods()->get() as $period) {
                $this->publishPeriod($period, $actor);
            }

            $this->auditor->record(
                AuditAction::AcademicCalendarPublished,
                $academicYear,
                [
                    'starts_on' => $academicYear->starts_on->toDateString(),
                    'ends_on' => $academicYear->ends_on->toDateString(),
                    'period_count' => $academicYear->academicPeriods()->count(),
                ],
                $actor,
            );

            return $academicYear->refresh();
        }, attempts: 3);
    }

    private function publishPeriod(AcademicPeriod $period, ?User $actor): void
    {
        if ($period->starts_on === null || $period->ends_on === null) {
            throw new InvalidValueException("{$period->displayName} needs dates before this calendar can be published.");
        }

        $status = $this->statusFor($period->starts_on, $period->ends_on);
        $this->lifecycle->change($period, $status, $actor);

        if ($status->isFrozen()) {
            return;
        }

        foreach ($period->children()->get() as $child) {
            $this->publishPeriod($child, $actor);
        }
    }

    private function statusFor(Carbon $startsOn, Carbon $endsOn): AcademicPeriodStatus
    {
        $today = now()->startOfDay();

        if ($endsOn->lt($today)) {
            return AcademicPeriodStatus::Closed;
        }

        return $startsOn->gt($today) ? AcademicPeriodStatus::Scheduled : AcademicPeriodStatus::Open;
    }
}
