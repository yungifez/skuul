<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\User;

/**
 * Give the borrower more time with a copy.
 *
 * A campus decides how often this is allowed, and a copy that is already late
 * is not renewed: it comes back first.
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
    }
}
