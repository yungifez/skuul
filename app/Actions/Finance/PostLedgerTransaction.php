<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\LedgerAccount;
use App\Models\LedgerLine;
use App\Models\LedgerTransaction;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Write one balanced entry into the books.
 *
 * The office works with invoices and receipts. This action turns each of those
 * into the debits and credits behind them, and refuses anything that does not
 * balance, so the books cannot drift.
 */
class PostLedgerTransaction
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Post the entry.
     *
     * @param  array<int, array{account: LedgerAccount|int, debit?: float, credit?: float, memo?: string|null, student_record_id?: int|null, program_id?: int|null, fund?: string|null}>  $lines
     *
     * @throws InvalidValueException when the entry does not balance
     */
    public function post(
        string $description,
        array $lines,
        ?CarbonInterface $date = null,
        ?Model $source = null,
        ?User $actor = null,
        ?string $reference = null,
        ?LedgerTransaction $reversalOf = null,
    ): LedgerTransaction {
        $prepared = $this->prepare($lines);

        return DB::transaction(function () use ($description, $prepared, $date, $source, $actor, $reference, $reversalOf): LedgerTransaction {
            $transaction = LedgerTransaction::create([
                'school_id' => $prepared['school_id'],
                'reference' => $reference,
                'description' => $description,
                'transaction_date' => $date ?? now(),
                'source_type' => $source?->getMorphClass(),
                'source_id' => $source?->getKey(),
                'reversal_of_id' => $reversalOf?->id,
                'posted_at' => now(),
                'posted_by' => $actor === null ? auth()->id() : $actor->id,
            ]);

            foreach ($prepared['lines'] as $line) {
                LedgerLine::create([
                    'ledger_transaction_id' => $transaction->id,
                    'ledger_account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'memo' => $line['memo'],
                    'student_record_id' => $line['student_record_id'],
                    'program_id' => $line['program_id'],
                    'fund' => $line['fund'],
                ]);
            }

            $this->auditor->record(
                AuditAction::LedgerTransactionPosted,
                $transaction,
                [
                    'description' => $description,
                    'total' => $prepared['total'],
                    'reversal_of_id' => $reversalOf?->id,
                ],
                $actor,
            );

            return $transaction;
        });
    }

    /**
     * Read the lines, check them, and say what the entry is worth.
     *
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{school_id: int, total: float, lines: array<int, array{account_id: int, debit: float, credit: float, memo: string|null, student_record_id: int|null, program_id: int|null, fund: string|null}>}
     *
     * @throws InvalidValueException
     */
    private function prepare(array $lines): array
    {
        if (count($lines) < 2) {
            throw new InvalidValueException('An entry needs at least two lines.');
        }

        $prepared = [];
        $debit = 0.0;
        $credit = 0.0;
        $schoolIds = [];

        foreach ($lines as $line) {
            $account = $line['account'] instanceof LedgerAccount
                ? $line['account']
                : LedgerAccount::findOrFail($line['account']);

            $lineDebit = round((float) ($line['debit'] ?? 0), 2);
            $lineCredit = round((float) ($line['credit'] ?? 0), 2);

            if ($lineDebit < 0 || $lineCredit < 0) {
                throw new InvalidValueException('A line cannot hold a negative amount.');
            }

            if (($lineDebit > 0) === ($lineCredit > 0)) {
                throw new InvalidValueException('Each line is either a debit or a credit, never both and never nothing.');
            }

            $schoolIds[] = $account->school_id;
            $debit += $lineDebit;
            $credit += $lineCredit;

            $prepared[] = [
                'account_id' => $account->id,
                'debit' => $lineDebit,
                'credit' => $lineCredit,
                'memo' => $line['memo'] ?? null,
                'student_record_id' => $line['student_record_id'] ?? null,

                // The dimensions a budget is written against, so a plan and
                // what happened can be compared on the same footing.
                'program_id' => $line['program_id'] ?? null,
                'fund' => $line['fund'] ?? null,
            ];
        }

        if (count(array_unique($schoolIds)) > 1) {
            throw new InvalidValueException('One entry cannot cross two schools.');
        }

        if (round($debit, 2) !== round($credit, 2)) {
            throw new InvalidValueException("This entry does not balance: $debit debited against $credit credited.");
        }

        if (round($debit, 2) <= 0) {
            throw new InvalidValueException('An entry cannot be worth nothing.');
        }

        return [
            'school_id' => $schoolIds[0],
            'total' => round($debit, 2),
            'lines' => $prepared,
        ];
    }
}
