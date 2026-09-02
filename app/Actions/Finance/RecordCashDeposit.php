<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\CashDeposit;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\FinancialPeriodResolver;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class RecordCashDeposit
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private FinancialPeriodResolver $periods,
        private RecordAuditEvent $auditor,
    ) {}

    /** @throws InvalidValueException */
    public function record(
        float $amount,
        CarbonInterface $date,
        ?string $bankReference = null,
        ?string $note = null,
        ?User $actor = null,
    ): CashDeposit {
        if ($amount <= 0) {
            throw new InvalidValueException('A cash deposit must be more than nothing.');
        }

        $period = $this->periods->openFor(current_school_id(), $date);
        $amount = round($amount, 2);

        return DB::transaction(function () use ($amount, $date, $bankReference, $note, $actor, $period): CashDeposit {
            $transaction = $this->post->post(
                description: 'Cash deposited into bank',
                lines: [
                    ['account' => $this->chart->account('bank'), 'debit' => $amount, 'memo' => $bankReference],
                    ['account' => $this->chart->account('cash'), 'credit' => $amount, 'memo' => 'Cash deposited into bank'],
                ],
                date: $date,
                actor: $actor,
                reference: $bankReference,
                period: $period,
            );

            $deposit = CashDeposit::create([
                'school_id' => current_school_id(),
                'financial_period_id' => $period->id,
                'ledger_transaction_id' => $transaction->id,
                'amount' => $amount,
                'deposit_date' => $date,
                'bank_reference' => $bankReference,
                'note' => $note,
                'recorded_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            $this->auditor->record(AuditAction::CashDepositRecorded, $deposit, ['amount' => $amount], $actor, current_school_id());

            return $deposit;
        });
    }
}
