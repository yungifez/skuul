<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\FeeInvoice;
use App\Models\LedgerAccount;
use App\Models\LedgerLine;
use App\Models\School;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\FinancialPeriodResolver;
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
        private FinancialPeriodResolver $periods,
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
     * Carry what a campus of the group wrote about a learner who left it.
     *
     * A payment taken or taken back at the old campus after a move lands in
     * the old campus's books. Carried on to the campus the learner attends,
     * it counts where the learner's account is kept.
     *
     * @return array<string, float>
     */
    public function carryToWhereTheyAttend(StudentRecord $enrollment, int $fromSchoolId, ?User $actor = null): array
    {
        $attending = StudentRecord::query()->whereKey($enrollment->id)->value('school_id');

        if ($attending === null || (int) $attending === $fromSchoolId) {
            return [];
        }

        return $this->carryIfTheyBillTogether(
            $enrollment,
            School::query()->findOrFail($fromSchoolId),
            School::query()->findOrFail($attending),
            $actor,
        );
    }

    /**
     * Carry what taking back a payment changed at a campus the learner left.
     *
     * While the two campuses still keep one purse, the whole balance follows
     * the learner as on any other day. After they stop billing together, only
     * the part of this payment that an earlier carry took across comes back
     * through the same path, so the old campus does not chase a debt the new
     * campus now holds, and the new campus does not keep credit that bounced.
     * Money the old campus billed or took on its own stays there.
     *
     * @return array<string, float>
     */
    public function carryTakenBack(StudentPayment $payment, ?User $actor = null): array
    {
        $enrollment = $payment->studentRecord;
        $attending = StudentRecord::query()->whereKey($enrollment->id)->value('school_id');

        if ($attending === null || (int) $attending === $payment->school_id) {
            return [];
        }

        $from = School::query()->findOrFail($payment->school_id);
        $to = School::query()->findOrFail($attending);

        if ($from->billsWith($to)) {
            return $this->carry($enrollment, $from, $to, $actor);
        }

        if (!$this->wasCarriedAfter($payment, $enrollment, $from) || !$this->wasCarriedAfter($payment, $enrollment, $to)) {
            return [];
        }

        $owed = round(min(
            max(0.0, $this->balanceOf('fees_receivable', $enrollment, $from)),
            $payment->allocations()
                ->whereHas('feeInvoice', fn ($invoice) => $invoice->where('school_id', $to->id))
                ->sum('amount') / 100,
        ), 2);
        $credit = round(min(
            max(0.0, -$this->balanceOf('unapplied_credits', $enrollment, $from)),
            $payment->unallocated()->getAmount()->toFloat(),
        ), 2);

        $carried = array_filter(['fees_receivable' => $owed, 'unapplied_credits' => -$credit], fn (float $amount): bool => $amount !== 0.0);

        foreach ($carried as $purpose => $amount) {
            $this->moveOne($purpose, $amount, $enrollment, $from, $to, $actor);
        }

        if ($carried !== []) {
            $this->auditor->record(
                AuditAction::BalanceCarriedToCampus,
                $enrollment,
                ['from_school_id' => $from->id, 'to_school_id' => $to->id, 'carried' => $carried, 'payment_id' => $payment->id],
                $actor,
                $to,
            );
        }

        return $carried;
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

            $this->moveOpenInvoices($enrollment, $from, $to);

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
        // A balance below nothing, such as credit taken back after it was
        // carried, moves the other way round.
        $isDebitBalance = ($leaving->type->normalBalance() === 'debit') === ($amount > 0);
        $amount = abs($amount);
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
     * Send the posted bills still owed along with the debt.
     *
     * The debt now sits in the new campus's books, so its bills must be paid
     * there. Left behind, a payment at the new campus finds nothing to settle,
     * and a payment at the old campus clears a debt its books no longer hold.
     */
    private function moveOpenInvoices(StudentRecord $enrollment, School $from, School $to): void
    {
        $invoices = FeeInvoice::query()
            ->where('student_record_id', $enrollment->id)
            ->where('school_id', $from->id)
            ->whereNotNull('ledger_transaction_id')
            ->isDue()
            ->lockForUpdate()
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        // The debt entered the new campus's books in its open period, so the
        // bills are listed there too.
        $period = $this->periods->openFor($to->id, now());

        $invoices->each(fn (FeeInvoice $invoice) => $invoice->forceFill([
            'school_id' => $to->id,
            'financial_period_id' => $period->id,
        ])->save());
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
     * Check whether a carry for this learner touched the campus after the payment.
     */
    private function wasCarriedAfter(StudentPayment $payment, StudentRecord $enrollment, School $school): bool
    {
        if ($payment->ledger_transaction_id === null) {
            return false;
        }

        return LedgerLine::query()
            ->where('student_record_id', $enrollment->id)
            ->whereIn('ledger_account_id', [
                $this->chart->account('due_from_campus', $school->id)->id,
                $this->chart->account('due_to_campus', $school->id)->id,
            ])
            ->where('ledger_transaction_id', '>', $payment->ledger_transaction_id)
            ->exists();
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
