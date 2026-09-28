<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Give the borrower more time with a copy.
 *
 * A campus decides how often this is allowed, and a copy that is already late
 * is not renewed: it comes back first. So does a copy somebody else is
 * waiting for, and a copy held by somebody who has left the campus.
 */
class RenewLoan
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Push the due date out by one more loan period.
     *
     * @throws InvalidValueException when the campus does not allow it
     */
    public function renew(LibraryLoan $loan, ?User $actor = null): LibraryLoan
    {
        return DB::transaction(function () use ($loan, $actor): LibraryLoan {
            $loan = LibraryLoan::query()->lockForUpdate()->findOrFail($loan->id);

            if (!$loan->isOpen()) {
                throw new InvalidValueException('This copy is already back.');
            }

            $policy = LibraryLendingRules::forSchool($loan->school_id);

            if ($policy->renewals_allowed === 0) {
                throw new InvalidValueException('This library does not renew loans.');
            }

            if ($loan->renewals >= $policy->renewals_allowed) {
                throw new InvalidValueException('This loan has been renewed as often as the library allows.');
            }

            if ($loan->daysLate() > 0) {
                throw new InvalidValueException('This copy is late. Bring it back before it goes out again.');
            }

            $this->refuseABorrowerWhoLeft($loan);
            $this->refuseACopySomebodyIsWaitingFor($loan);

            $was = $loan->due_on->toDateString();
            $loan->due_on = $loan->due_on->copy()->addDays($policy->loan_days);
            $loan->renewals = $loan->renewals + 1;
            $loan->save();

            $this->auditor->record(
                AuditAction::LibraryLoanRenewed,
                $loan,
                ['was' => $was, 'now' => $loan->due_on->toDateString(), 'renewals' => $loan->renewals],
                $actor,
                $loan->school_id,
            );

            return $loan;
        });
    }

    /**
     * Refuse more time to a borrower who no longer belongs to the campus.
     *
     * @throws InvalidValueException
     */
    private function refuseABorrowerWhoLeft(LibraryLoan $loan): void
    {
        $borrower = $loan->borrower;

        if ($borrower === null
            || !$borrower->belongsToSchool($loan->school_id)
            || $borrower->isLearnerWhoNoLongerAttends($loan->school_id)) {
            throw new InvalidValueException('The borrower has left this campus. The copy must come back.');
        }
    }

    /**
     * Refuse more time when somebody else is in the queue for the title.
     *
     * @throws InvalidValueException
     */
    private function refuseACopySomebodyIsWaitingFor(LibraryLoan $loan): void
    {
        $somebodyIsWaiting = LibraryReservation::query()
            ->where('school_id', $loan->school_id)
            ->where('library_title_id', $loan->copy?->library_title_id)
            ->where('status', LibraryReservationStatus::Waiting->value)
            ->where('user_id', '!=', $loan->user_id)
            ->exists();

        if ($somebodyIsWaiting) {
            throw new InvalidValueException('Somebody is waiting for this title. Bring the copy back so it can go to them.');
        }
    }
}
