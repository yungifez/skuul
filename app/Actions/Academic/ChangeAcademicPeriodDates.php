<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Move the dates of an academic period, and record that it happened.
 *
 * Dates on a draft are just configuration. Dates on a period people are
 * already teaching in decide which period a record belongs to, so moving them
 * is a deliberate act with a reason and an audit event behind it.
 *
 * A frozen period keeps its dates. Reopen it first.
 */
class ChangeAcademicPeriodDates
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Set the first and last day of the period.
     *
     * @throws InvalidValueException when the dates are impossible or the
     *                               period is frozen
     */
    public function change(
        AcademicYear|AcademicPeriod $period,
        ?Carbon $startsOn,
        ?Carbon $endsOn,
        ?User $actor = null,
        ?string $reason = null,
    ): AcademicYear|AcademicPeriod {
        if ($period->status->isFrozen()) {
            throw new InvalidValueException('A closed period keeps its dates. Reopen it before you change them.');
        }

        if ($startsOn !== null && $endsOn !== null && $endsOn->lt($startsOn)) {
            throw new InvalidValueException('A period cannot end before it starts.');
        }

        $wasInUse = $period->status->acceptsWrites();

        if ($wasInUse && trim((string) $reason) === '') {
            throw new InvalidValueException('Say why the dates of a period already in use are changing.');
        }

        $this->refuseMovingOutsideTheCycle($period, $startsOn, $endsOn);

        $before = [
            'starts_on' => $period->starts_on?->toDateString(),
            'ends_on' => $period->ends_on?->toDateString(),
        ];

        $after = [
            'starts_on' => $startsOn?->toDateString(),
            'ends_on' => $endsOn?->toDateString(),
        ];

        if ($before === $after) {
            return $period;
        }

        return DB::transaction(function () use ($period, $startsOn, $endsOn, $before, $after, $actor, $reason): AcademicYear|AcademicPeriod {
            $period->starts_on = $startsOn;
            $period->ends_on = $endsOn;
            $period->save();

            $this->auditor->record(
                AuditAction::AcademicPeriodDatesChanged,
                $period,
                ['from' => $before, 'to' => $after, 'reason' => $reason],
                $actor,
            );

            return $period;
        });
    }

    /**
     * Stop a period from running outside the cycle that holds it.
     *
     * A term that ends after its year does is a term whose records belong to
     * no cycle, which breaks every report that groups by year.
     *
     * @throws InvalidValueException
     */
    private function refuseMovingOutsideTheCycle(
        AcademicYear|AcademicPeriod $period,
        ?Carbon $startsOn,
        ?Carbon $endsOn,
    ): void {
        if (!$period instanceof AcademicPeriod) {
            return;
        }

        $container = $period->parent ?? $period->academicYear;

        if ($container === null || $container->starts_on === null || $container->ends_on === null) {
            return;
        }

        $outside = ($startsOn !== null && $startsOn->lt($container->starts_on))
            || ($endsOn !== null && $endsOn->gt($container->ends_on));

        if ($outside) {
            throw new InvalidValueException(
                "This period must run inside {$container->starts_on->toDateString()} to {$container->ends_on->toDateString()}."
            );
        }
    }
}
