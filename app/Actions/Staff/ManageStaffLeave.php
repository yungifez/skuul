<?php

namespace App\Actions\Staff;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Models\StaffLeaveRequest;
use App\Models\StaffLeaveStatusChange;
use App\Models\StaffProfile;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ask for leave, answer the request, and keep the history.
 *
 * Leave belongs to employment, not to teaching. Ending a teaching assignment
 * is a separate decision that the curriculum action makes.
 */
class ManageStaffLeave
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Ask for days away.
     *
     * @throws InvalidValueException when the dates are backwards, the person has left, or the days are already held
     */
    public function request(
        StaffProfile $profile,
        CarbonInterface|string $startsOn,
        CarbonInterface|string $endsOn,
        LeaveType $type = LeaveType::Annual,
        ?string $reason = null,
        ?User $actor = null,
    ): StaffLeaveRequest {
        $start = Carbon::parse($startsOn)->startOfDay();
        $end = Carbon::parse($endsOn)->startOfDay();

        if ($end->lt($start)) {
            throw new InvalidValueException('Leave cannot end before it starts.');
        }

        if ($profile->status === StaffStatus::Left) {
            throw new InvalidValueException('This person no longer works here.');
        }

        $clash = StaffLeaveRequest::query()
            ->where('staff_profile_id', $profile->id)
            ->holding()
            ->overlapping($start, $end)
            ->exists();

        if ($clash) {
            throw new InvalidValueException('These days are already asked for.');
        }

        return DB::transaction(function () use ($profile, $start, $end, $type, $reason, $actor): StaffLeaveRequest {
            $request = StaffLeaveRequest::create([
                'school_id'        => $profile->school_id,
                'staff_profile_id' => $profile->id,
                'type'             => $type,
                'starts_on'        => $start,
                'ends_on'          => $end,
                'reason'           => $reason,
                'requested_by'     => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::StaffLeaveRequested,
                $request,
                ['type' => $type->value, 'days' => $request->days()],
                $actor,
            );

            return $request;
        });
    }

    /**
     * Answer the request or record what happened.
     *
     * @throws InvalidValueException when the state cannot follow the current one
     */
    public function changeStatus(
        StaffLeaveRequest $request,
        LeaveStatus $status,
        ?User $actor = null,
        ?string $reason = null,
    ): StaffLeaveRequest {
        $current = $request->status;

        if ($current === $status) {
            return $request;
        }

        if (!$current->canMoveTo($status)) {
            throw new InvalidValueException("Leave cannot move from {$current->value} to {$status->value}.");
        }

        return DB::transaction(function () use ($request, $current, $status, $actor, $reason): StaffLeaveRequest {
            $request->status = $status;

            if (in_array($status, [LeaveStatus::Approved, LeaveStatus::Declined], true)) {
                $request->decided_by = $actor === null ? auth()->id() : $actor->id;
                $request->decided_at = now();
                $request->decision_note = $reason;
            }

            $request->save();

            StaffLeaveStatusChange::create([
                'staff_leave_request_id' => $request->id,
                'from_status'            => $current,
                'to_status'              => $status,
                'reason'                 => $reason,
                'changed_by'             => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(
                AuditAction::StaffLeaveStatusChanged,
                $request,
                ['from' => $current->value, 'to' => $status->value, 'reason' => $reason],
                $actor,
            );

            return $request;
        });
    }

    /**
     * Agree to the days.
     */
    public function approve(StaffLeaveRequest $request, ?User $actor = null, ?string $note = null): StaffLeaveRequest
    {
        return $this->changeStatus($request, LeaveStatus::Approved, $actor, $note);
    }

    /**
     * Say no to the days.
     */
    public function decline(StaffLeaveRequest $request, ?User $actor = null, ?string $note = null): StaffLeaveRequest
    {
        return $this->changeStatus($request, LeaveStatus::Declined, $actor, $note);
    }

    /**
     * Withdraw the request.
     */
    public function cancel(StaffLeaveRequest $request, ?User $actor = null, ?string $note = null): StaffLeaveRequest
    {
        return $this->changeStatus($request, LeaveStatus::Cancelled, $actor, $note);
    }
}
