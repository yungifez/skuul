<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LedgerAccount;
use App\Models\LedgerLine;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use Illuminate\Support\Facades\DB;

/**
 * Move what a learner owes, or is owed, to the campus they now attend.
 *
 * Campuses that keep one purse bill a family as one school, so a learner who
 * moves must not leave a debt behind at a campus that will never see them
 * again. Each campus's own books still balance: the two campuses settle with
 * each other through the "due from" and "due to" accounts.
 *
 * Campuses that keep separate books carry nothing. Money never moves between
 * organizations by itself.
 */
class CarryBalanceToCampus
{
    /**
     * What follows the learner, and what the money means at each campus.
     *
     * @var array<int, string>
     */
    private const CARRIED = ['fees_receivable', 'unapplied_credits'];

    public function __construct(
        private ChartOfAccounts $chart,
        private PostLedgerTransaction $post,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Carry the balances when the two campuses keep one purse.
     *
     * Returns what was carried, by purpose. Nothing is posted when the
     * campuses bill separately or the learner owes and is owed nothing.
     *
     * @return array<string, float>
     */
    public function carryIfTheyBillTogether(
        StudentRecord $enrollment,
        School $from,
        School $to,
        ?User $actor = null,
    ): array {
        if (!$from->billsWith($to) || $from->id === $to->id) {
            return [];
        }

        return $this->carry($enrollment, $from, $to, $actor);
    }

    /**
     * Carry the balances from one campus to the other.
     *
     * @return array<string, float>
     *
     * @throws InvalidValueException when the two campuses keep separate books
     */
    public function carry(StudentRecord $enrollment, School $from, School $to, ?User $actor = null): array
    {
        if (!$from->billsWith($to)) {
            throw new InvalidValueException(
                "$from->name and $to->name keep separate books, so money cannot be moved between them."
            );
        }

        return DB::transaction(function () use ($enrollment, $from, $to, $actor): array {
            $carried = [];

            foreach (self::CARRIED as $purpose) {
                $amount = $this->balanceOf($purpose, $enrollment, $from);

                if ($amount === 0.0) {
                    continue;
                }

                $this->moveOne($purpose, $amount, $enrollment, $from, $to, $actor);
                $carried[$purpose] = $amount;
            }

            if ($carried !== []) {
                $this->auditor->record(
                    AuditAction::BalanceCarriedToCampus,
                    $enrollment,
                    ['from_school_id' => $from->id, 'to_school_id' => $to->id, 'carried' => $carried],
                    $actor,
                    $to,
                );
            }

            return $carried;
        });
    }

    /**
     * Post the pair of entries that move one balance.
     *
     * At the campus the learner is leaving the balance is cleared. At the
     * campus they are joining the same balance is written again. Each campus
     * balances its own entry against the other campus.
     */
    private function moveOne(
        string $purpose,
        float $amount,
        StudentRecord $enrollment,
        School $from,
        School $to,
        ?User $actor,
    ): void {
        $leaving = $this->chart->account($purpose, $from->id);
        $joining = $this->chart->account($purpose, $to->id);
        $isDebitBalance = $leaving->type->normalBalance() === 'debit';
        $memo = "Carried to $to->name";

        // Clearing a debit balance is a credit, and the campus is then owed by
        // the other campus. Clearing a credit balance is the other way round.
        $this->post->post(
            description: "Balance carried to $to->name",
            lines: [
                $this->line($leaving, $isDebitBalance ? 0.0 : $amount, $isDebitBalance ? $amount : 0.0, $enrollment, $memo),
                $this->line(
                    $this->chart->account($isDebitBalance ? 'due_from_campus' : 'due_to_campus', $from->id),
                    $isDebitBalance ? $amount : 0.0,
                    $isDebitBalance ? 0.0 : $amount,
                    $enrollment,
                    $memo,
                ),
            ],
            source: $enrollment,
            actor: $actor,
        );

        $this->post->post(
            description: "Balance carried from $from->name",
            lines: [
                $this->line($joining, $isDebitBalance ? $amount : 0.0, $isDebitBalance ? 0.0 : $amount, $enrollment, "Carried from $from->name"),
                $this->line(
                    $this->chart->account($isDebitBalance ? 'due_to_campus' : 'due_from_campus', $to->id),
                    $isDebitBalance ? 0.0 : $amount,
                    $isDebitBalance ? $amount : 0.0,
                    $enrollment,
                    "Carried from $from->name",
                ),
            ],
            source: $enrollment,
            actor: $actor,
        );
    }

    /**
     * Build one line of an entry.
     *
     * @return array{account: LedgerAccount, debit: float, credit: float, memo: string, student_record_id: int}
     */
    private function line(LedgerAccount $account, float $debit, float $credit, StudentRecord $enrollment, string $memo): array
    {
        return [
            'account' => $account,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'memo' => $memo,
            'student_record_id' => $enrollment->id,
        ];
    }

    /**
     * Get what one account of one campus says about this learner.
     */
    private function balanceOf(string $purpose, StudentRecord $enrollment, School $school): float
    {
        $account = $this->chart->account($purpose, $school->id);

        $lines = LedgerLine::query()
            ->where('ledger_account_id', $account->id)
            ->where('student_record_id', $enrollment->id);

        $debit = (float) (clone $lines)->sum('debit');
        $credit = (float) (clone $lines)->sum('credit');

        return round($account->type->normalBalance() === 'debit' ? $debit - $credit : $credit - $debit, 2);
    }
}
