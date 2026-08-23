<?php

namespace App\Actions\Library;

use App\Actions\Audit\RecordAuditEvent;
use App\Actions\Finance\ChargeStudent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Take a copy back, and charge for it if it is late.
 *
 * A fine is not a separate pot of library money. It goes through the same
 * charge as every other thing a school bills for, so one balance answers what
 * a family owes.
 */
class ReturnLoan
{
    public function __construct(
        private ChargeStudent $charge,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Record the copy coming back.
     *
     * @throws InvalidValueException when the copy is already back
     */
    public function receive(LibraryLoan $loan, ?User $actor = null, ?Carbon $returnedOn = null): LibraryLoan
    {
        if (!$loan->isOpen()) {
            throw new InvalidValueException('This copy is already back.');
        }

        $returnedOn ??= now();

        return DB::transaction(function () use ($loan, $actor, $returnedOn): LibraryLoan {
            $loan->returned_on = $returnedOn;
            $loan->received_by = $actor === null ? auth()->id() : $actor->id;

            $fine = $this->fineFor($loan, $returnedOn->toDateString());
            $loan->fine_charged = $fine;

            $loan->save();

            if ($fine > 0) {
                $this->chargeTheFine($loan, $fine, $actor);
            }

            $this->auditor->record(
                AuditAction::LibraryLoanReturned,
                $loan,
                [
                    'days_late' => $loan->daysLate(),
                    'fine' => $fine,
                ],
                $actor,
                $loan->school_id,
            );

            return $loan;
        });
    }

    /**
     * Work out what the late days cost, in minor units.
     */
    private function fineFor(LibraryLoan $loan, string $returnedOn): int
    {
        $policy = LibraryLendingRules::forSchool($loan->school_id);

        if (!$policy->chargesFines()) {
            return 0;
        }

        // Both sides are whole days. A copy due today and brought back today
        // is not late by a few hours.
        $due = $loan->due_on->copy()->startOfDay();
        $back = now()->parse($returnedOn)->startOfDay();

        $late = $due->greaterThanOrEqualTo($back) ? 0 : (int) $due->diffInDays($back);

        return $late * $policy->fine_per_day;
    }

    /**
     * Put the fine on the borrower's account.
     *
     * Only a learner has a fee account. A member of staff who keeps a book
     * too long is a conversation, not a charge.
     */
    private function chargeTheFine(LibraryLoan $loan, int $fine, ?User $actor): void
    {
        $enrollment = $loan->borrower?->studentRecord;

        if ($enrollment === null) {
            return;
        }

        $this->charge->charge(
            enrollment: $enrollment,
            amount: round($fine / 100, 2),
            description: 'Library fine',
            source: $loan,
            actor: $actor,
            incomePurpose: 'other_income',
        );
    }
}
