<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicPeriodStatusChange;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\Calendar\ClosureReadinessCheck;
use Illuminate\Support\Facades\DB;

/**
 * Move an academic period between states and record who did it.
 *
 * The rules a school depends on live here:
 *
 * - Closing a cycle closes the periods inside it, because a period cannot
 *   accept work its cycle no longer accepts.
 * - Reopening needs an open parent, for the same reason in reverse.
 * - Undoing a close needs a stated reason. Nothing else does.
 * - A final close records what the readiness check found at that moment.
 *
 * Repeating a request changes nothing and adds no second history record.
 *
 * Finance is untouched. Closing a period never closes an invoice, a payment,
 * or a ledger transaction.
 */
class ChangeAcademicPeriodStatus
{
    public function __construct(
        private RecordAuditEvent $auditor,
        private ClosureReadinessCheck $readiness,
    ) {
    }

    /**
     * Move the period to the given state.
     *
     * @param array<int, array<string, mixed>>|null $checklist what the readiness check found
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function change(
        AcademicYear|AcademicPeriod $period,
        AcademicPeriodStatus $status,
        ?User $actor = null,
        ?string $reason = null,
        ?array $checklist = null,
    ): AcademicYear|AcademicPeriod {
        $current = $period->status;

        if ($current === $status) {
            return $period;
        }

        $this->refuseImpossibleMove($period, $current, $status, $reason);

        return DB::transaction(function () use ($period, $current, $status, $actor, $reason, $checklist): AcademicYear|AcademicPeriod {
            $period->status = $status;
            $period->save();

            AcademicPeriodStatusChange::create([
                'period_type' => $period->getMorphClass(),
                'period_id'   => $period->id,
                'from_status' => $current,
                'to_status'   => $status,
                'changed_by'  => $actor?->id,
                'reason'      => $reason,
                'checklist'   => $checklist,
            ]);

            $this->auditor->record(
                AuditAction::AcademicPeriodStatusChanged,
                $period,
                [
                    'from'      => $current->value,
                    'to'        => $status->value,
                    'reason'    => $reason,
                    'checklist' => $checklist,
                ],
                $actor,
            );

            $this->cascade($period, $status, $actor, $reason);

            return $period;
        });
    }

    /**
     * Stop a move the calendar does not allow.
     *
     * @throws InvalidValueException
     */
    private function refuseImpossibleMove(
        AcademicYear|AcademicPeriod $period,
        AcademicPeriodStatus $current,
        AcademicPeriodStatus $status,
        ?string $reason,
    ): void {
        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException(
                "An academic period cannot move from {$current->value} to {$status->value}."
            );
        }

        if ($current->requiresReasonToReach($status) && trim((string) $reason) === '') {
            throw new InvalidValueException('Say why this period is being reopened.');
        }

        if (!$period instanceof AcademicPeriod || $status->isFrozen()) {
            return;
        }

        if ($period->academicYear?->isClosed()) {
            throw new InvalidValueException('Reopen the academic year before you reopen this period.');
        }

        if ($period->parent?->isClosed()) {
            throw new InvalidValueException("Reopen {$period->parent->displayName} before you reopen this period.");
        }
    }

    /**
     * Carry a cycle's new state down to the periods inside it.
     *
     * Only a freeze cascades. Opening a cycle does not open every period in
     * it, because a cycle opens long before its last term starts.
     */
    private function cascade(
        AcademicYear|AcademicPeriod $period,
        AcademicPeriodStatus $status,
        ?User $actor,
        ?string $reason,
    ): void {
        if (!$status->isFrozen()) {
            return;
        }

        $children = $period instanceof AcademicYear
            ? $period->topLevelPeriods()->get()
            : $period->children()->get();

        foreach ($children as $child) {
            if ($child->status === $status || !$child->status->canMoveTo($status)) {
                continue;
            }

            $this->change($child, $status, $actor, $reason);
        }
    }

    /**
     * Move the period into its closing window.
     *
     * Work that is already started may finish. Nothing new begins.
     */
    public function beginClosing(AcademicYear|AcademicPeriod $period, ?User $actor = null, ?string $reason = null): AcademicYear|AcademicPeriod
    {
        return $this->change($period, AcademicPeriodStatus::Closing, $actor, $reason);
    }

    /**
     * Finish the period. Its records become read-only.
     *
     * The readiness check runs first and is stored with the close. A blocking
     * finding stops the close until a person passes `force`, which is the
     * deliberate act of accepting the outstanding work.
     *
     * @throws InvalidValueException when work is outstanding and force is false
     */
    public function close(
        AcademicYear|AcademicPeriod $period,
        ?User $actor = null,
        ?string $reason = null,
        bool $force = false,
    ): AcademicYear|AcademicPeriod {
        $checklist = $this->readiness->snapshot($period);

        $blocking = array_filter($checklist, fn (array $finding): bool => $finding['blocking']);

        if ($blocking !== [] && !$force) {
            $summaries = implode(' ', array_column($blocking, 'summary'));

            throw new InvalidValueException("This period is not ready to close. {$summaries}");
        }

        return $this->change($period, AcademicPeriodStatus::Closed, $actor, $reason, $checklist);
    }

    /**
     * Let the period accept work again.
     *
     * @throws InvalidValueException when no reason is given
     */
    public function reopen(AcademicYear|AcademicPeriod $period, ?User $actor = null, ?string $reason = null): AcademicYear|AcademicPeriod
    {
        return $this->change($period, AcademicPeriodStatus::Open, $actor, $reason);
    }

    /**
     * Move a closed period out of the way, keeping it readable.
     */
    public function archive(AcademicYear|AcademicPeriod $period, ?User $actor = null, ?string $reason = null): AcademicYear|AcademicPeriod
    {
        return $this->change($period, AcademicPeriodStatus::Archived, $actor, $reason);
    }

    /**
     * Agree the dates and wait for the start day.
     */
    public function schedule(AcademicYear|AcademicPeriod $period, ?User $actor = null, ?string $reason = null): AcademicYear|AcademicPeriod
    {
        return $this->change($period, AcademicPeriodStatus::Scheduled, $actor, $reason);
    }

    /**
     * Start the period.
     */
    public function open(AcademicYear|AcademicPeriod $period, ?User $actor = null, ?string $reason = null): AcademicYear|AcademicPeriod
    {
        return $this->change($period, AcademicPeriodStatus::Open, $actor, $reason);
    }
}
