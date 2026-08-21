<?php

namespace App\Actions\Finance;

use App\Exceptions\InvalidValueException;
use App\Models\LedgerTransaction;
use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Undo a posted entry by posting its mirror.
 *
 * The first entry stays in the books. The reversal cancels it, so anyone
 * reading the books later can see both what was recorded and what corrected
 * it.
 */
class ReverseLedgerTransaction
{
    public function __construct(private PostLedgerTransaction $post)
    {
    }

    /**
     * Post the reversal of the entry.
     *
     * @throws InvalidValueException when the entry was already reversed
     */
    public function reverse(
        LedgerTransaction $transaction,
        string $reason,
        ?User $actor = null,
        ?CarbonInterface $date = null,
    ): LedgerTransaction {
        if ($transaction->isReversed()) {
            throw new InvalidValueException('This entry was already reversed.');
        }

        if ($transaction->reversal_of_id !== null) {
            throw new InvalidValueException('A reversal cannot be reversed. Post the entry again instead.');
        }

        $lines = $transaction->lines()->get()->map(fn ($line): array => [
            'account'           => $line->ledger_account_id,
            'debit'             => $line->credit,
            'credit'            => $line->debit,
            'memo'              => $line->memo,
            'student_record_id' => $line->student_record_id,
        ])->all();

        return $this->post->post(
            description: "Reversal: $transaction->description. $reason",
            lines: $lines,
            date: $date,
            source: $transaction->source,
            actor: $actor,
            reference: $transaction->reference,
            reversalOf: $transaction,
        );
    }
}
