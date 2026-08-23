<?php

namespace App\Services\Finance;

use App\Models\LedgerAccount;
use App\Models\LedgerLine;
use App\Models\School;
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
    public function __construct(private ChartOfAccounts $chart) {}

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
     * Get what the learner still owes at every campus that wrote about them.
     *
     * A learner who moved between campuses that keep separate books can owe
     * money at a campus they no longer attend. That debt belongs to the campus
     * that is owed it, so it is shown beside its campus rather than added in.
     *
     * @return Collection<int, array{school: School, balance: float}>
     */
    public function balancesByCampus(StudentRecord $enrollment): Collection
    {
        $accounts = LedgerAccount::query()
            ->forPurpose('fees_receivable')
            ->whereIn('id', LedgerLine::query()
                ->select('ledger_account_id')
                ->where('student_record_id', $enrollment->id))
            ->with('school')
            ->get();

        $balances = collect();

        foreach ($accounts as $account) {
            $school = $account->school;

            if ($school === null) {
                continue;
            }

            $balance = $this->balanceOfAccount($account, $enrollment);

            if ($balance !== 0.0) {
                $balances->push(['school' => $school, 'balance' => $balance]);
            }
        }

        return $balances;
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
        return $this->balanceOfAccount($this->chart->account($purpose, $enrollment->school_id), $enrollment);
    }

    /**
     * Get what one account says about one student.
     */
    private function balanceOfAccount(LedgerAccount $account, StudentRecord $enrollment): float
    {
        $lines = LedgerLine::query()
            ->where('ledger_account_id', $account->id)
            ->where('student_record_id', $enrollment->id);

        $debit = (float) (clone $lines)->sum('debit');
        $credit = (float) (clone $lines)->sum('credit');

        return round($account->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }
}
