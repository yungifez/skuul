<?php

namespace App\Actions\Academic;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriodStatusChange;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Close or reopen an academic period and record who did it.
 *
 * Closing an academic year closes the semesters inside it, because a semester
 * cannot accept work its year no longer accepts. Reopening a semester needs an
 * open year for the same reason. Repeating a request changes nothing and adds
 * no second history record.
 */
class ChangeAcademicPeriodStatus
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Move the period to the given state.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function change(AcademicYear|Semester $period, AcademicPeriodStatus $status, ?User $actor = null, ?string $reason = null): AcademicYear|Semester
    {
        $current = $period->status;

        if ($current === $status) {
            return $period;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException(
                "An academic period cannot move from {$current->value} to {$status->value}."
            );
        }

        if ($period instanceof Semester && $status === AcademicPeriodStatus::Open && $period->academicYear?->isClosed()) {
            throw new InvalidValueException('Reopen the academic year before you reopen this semester.');
        }

        return DB::transaction(function () use ($period, $current, $status, $actor, $reason): AcademicYear|Semester {
            $period->status = $status;
            $period->save();

            AcademicPeriodStatusChange::create([
                'period_type' => $period->getMorphClass(),
                'period_id'   => $period->id,
                'from_status' => $current,
                'to_status'   => $status,
                'changed_by'  => $actor?->id,
                'reason'      => $reason,
            ]);

            $this->auditor->record(
                AuditAction::AcademicPeriodStatusChanged,
                $period,
                ['from' => $current->value, 'to' => $status->value, 'reason' => $reason],
                $actor,
            );

            // A year carries its semesters with it when it closes.
            if ($period instanceof AcademicYear && $status === AcademicPeriodStatus::Closed) {
                foreach ($period->semesters()->get() as $semester) {
                    $this->change($semester, AcademicPeriodStatus::Closed, $actor, $reason);
                }
            }

            return $period;
        });
    }

    /**
     * Finish the period. Its records become read-only.
     */
    public function close(AcademicYear|Semester $period, ?User $actor = null, ?string $reason = null): AcademicYear|Semester
    {
        return $this->change($period, AcademicPeriodStatus::Closed, $actor, $reason);
    }

    /**
     * Let the period accept work again.
     */
    public function reopen(AcademicYear|Semester $period, ?User $actor = null, ?string $reason = null): AcademicYear|Semester
    {
        return $this->change($period, AcademicPeriodStatus::Open, $actor, $reason);
    }
}
