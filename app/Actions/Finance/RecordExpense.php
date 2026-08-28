<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Expense;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\FinancialPeriodResolver;
use App\Services\Finance\PaymentChannelRegistry;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class RecordExpense
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private FinancialPeriodResolver $periods,
        private PaymentChannelRegistry $channels,
        private RecordAuditEvent $auditor,
    ) {}

    /** @throws InvalidValueException */
    public function record(
        LedgerAccount $account,
        float $amount,
        string $description,
        string $method,
        CarbonInterface $date,
        ?string $vendor = null,
        ?string $reference = null,
        ?string $note = null,
        ?Program $program = null,
        ?string $fund = null,
        ?User $actor = null,
    ): Expense {
        if ($account->school_id !== current_school_id()) {
            throw new InvalidValueException('That expense account belongs to another school.');
        }

        if (!$account->type->isExpense()) {
            throw new InvalidValueException('Choose an expense account.');
        }

        if ($amount <= 0) {
            throw new InvalidValueException('An expense must be more than nothing.');
        }

        $channel = $this->channels->get($method);
        $period = $this->periods->openFor(current_school_id(), $date);
        $amount = round($amount, 2);

        return DB::transaction(function () use ($account, $amount, $description, $method, $channel, $date, $vendor, $reference, $note, $program, $fund, $actor, $period): Expense {
            $transaction = $this->post->post(
                description: $description,
                lines: [
                    ['account' => $account, 'debit' => $amount, 'program_id' => $program?->id, 'fund' => $fund, 'memo' => $description],
                    ['account' => $this->chart->account($channel->accountPurpose(), current_school_id()), 'credit' => $amount, 'program_id' => $program?->id, 'fund' => $fund, 'memo' => $channel->label()],
                ],
                date: $date,
                actor: $actor,
                reference: $reference,
                period: $period,
            );

            $expense = Expense::create([
                'school_id' => current_school_id(),
                'financial_period_id' => $period->id,
                'ledger_account_id' => $account->id,
                'ledger_transaction_id' => $transaction->id,
                'amount' => $amount,
                'expense_date' => $date,
                'description' => $description,
                'vendor' => $vendor,
                'method' => $method,
                'reference' => $reference,
                'note' => $note,
                'program_id' => $program?->id,
                'fund' => $fund,
                'recorded_by' => $actor?->id ?? auth()->id(),
            ]);

            $this->auditor->record(AuditAction::ExpenseRecorded, $expense, ['amount' => $amount, 'method' => $method], $actor, current_school_id());

            return $expense;
        });
    }
}
