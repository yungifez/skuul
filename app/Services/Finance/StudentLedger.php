<?php

namespace App\Services\Finance;

use App\Models\LedgerLine;
use App\Models\StudentRecord;
use Illuminate\Support\Collection;

/**
 * Answer what a student owes, from the books rather than from a stored total.
 *
 * A `paid` column can drift the moment two people press save at once. The
 * ledger lines cannot, because every change is another line.
 */
class StudentLedger
{
    public function __construct(private ChartOfAccounts $chart)
    {
    }

    /**
     * Get what the student still owes.
     *
     * A positive number is money due to the school.
     */
    public function balance(StudentRecord $enrollment): float
    {
        return $this->balanceOn('fees_receivable', $enrollment);
    }

    /**
     * Get the money the school holds that no invoice has used yet.
     */
    public function unappliedCredit(StudentRecord $enrollment): float
    {
        return $this->balanceOn('unapplied_credits', $enrollment);
    }

    /**
     * Get every line written about the student, oldest first.
     *
     * @return Collection<int, LedgerLine>
     */
    public function statement(StudentRecord $enrollment): Collection
    {
        return LedgerLine::query()
            ->where('student_record_id', $enrollment->id)
            ->with(['account', 'transaction'])
            ->orderBy('id')
            ->get();
    }

    /**
     * Get the balance of one account for one student.
     */
    private function balanceOn(string $purpose, StudentRecord $enrollment): float
    {
        $account = $this->chart->account($purpose, $enrollment->school_id);

        $lines = LedgerLine::query()
            ->where('ledger_account_id', $account->id)
            ->where('student_record_id', $enrollment->id);

        $debit = (float) (clone $lines)->sum('debit');
        $credit = (float) (clone $lines)->sum('credit');

        return round($account->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }
}
