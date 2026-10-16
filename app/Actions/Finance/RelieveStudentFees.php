<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\LedgerTransaction;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\StudentLedger;
use Brick\Money\Money as BrickMoney;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Take a charge off a student's account without money changing hands.
 *
 * A scholarship and a written-off debt look the same to the student and very
 * different to the school, so each one goes to its own expense account.
 */
class RelieveStudentFees
{
    public function __construct(
        private PostLedgerTransaction $post,
        private ChartOfAccounts $chart,
        private StudentLedger $ledger,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Take part of one line of a posted invoice off the student's account.
     *
     * The line's waiver grows, so the invoice shows what is still owed, and
     * the books of the campus that billed it record the cost. Only what is
     * still owed on the line, and on that campus's books, can be relieved.
     *
     * @param  int  $amount  what to take off, in minor units
     *
     * @throws InvalidValueException when the invoice is not posted or owes less
     */
    public function relieveLine(
        FeeInvoiceRecord $line,
        int $amount,
        bool $writeOff,
        string $reason,
        ?User $actor = null,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('The amount must be more than nothing.');
        }

        if (trim($reason) === '') {
            throw new InvalidValueException('Say why the fee is being taken off.');
        }

        return DB::transaction(function () use ($line, $amount, $writeOff, $reason, $actor): LedgerTransaction {
            $invoice = FeeInvoice::query()->whereKey($line->fee_invoice_id)->firstOrFail();
            $enrollment = $invoice->studentRecord;

            if ($enrollment === null || $invoice->ledger_transaction_id === null) {
                throw new InvalidValueException('Only an invoice in the books can be waived. Change an unposted invoice instead.');
            }

            // Every change to a learner's money locks their record first, so
            // a payment and a waiver cannot both settle the same fee.
            StudentRecord::query()->whereKey($enrollment->getKey())->lockForUpdate()->first();
            $line = FeeInvoiceRecord::query()->whereKey($line->getKey())->lockForUpdate()->firstOrFail();

            $owedOnLine = $line->outstanding->getMinorAmount()->toInt();
            $owedAtCampus = (int) round($this->ledger->balance($enrollment, $invoice->school_id) * 100);

            if ($amount > min($owedOnLine, $owedAtCampus)) {
                throw new InvalidValueException('That is more than is still owed on this fee.');
            }

            $line->waiver = $line->waiver->plus(BrickMoney::ofMinor($amount, config('app.currency')));
            $line->save();

            $transaction = $this->relieve(
                $enrollment,
                round($amount / 100, 2),
                $writeOff ? 'bad_debt' : 'scholarships',
                ($writeOff ? 'Write-off' : 'Waiver').": $reason",
                $invoice,
                $actor,
                null,
                $invoice->school_id,
            );

            $this->auditor->record(
                AuditAction::FeesRelieved,
                $invoice,
                [
                    'fee_invoice_record_id' => $line->id,
                    'amount' => $amount,
                    'kind' => $writeOff ? 'write_off' : 'waiver',
                    'reason' => $reason,
                ],
                $actor,
                $invoice->school_id,
            );

            return $transaction;
        });
    }

    /**
     * Give the student a scholarship or a waiver.
     */
    public function waive(
        StudentRecord $enrollment,
        float $amount,
        string $reason,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
    ): LedgerTransaction {
        return $this->relieve($enrollment, $amount, 'scholarships', "Waiver: $reason", $source, $actor, $date);
    }

    /**
     * Give up on collecting the money.
     */
    public function writeOff(
        StudentRecord $enrollment,
        float $amount,
        string $reason,
        ?Model $source = null,
        ?User $actor = null,
        ?CarbonInterface $date = null,
    ): LedgerTransaction {
        return $this->relieve($enrollment, $amount, 'bad_debt', "Write-off: $reason", $source, $actor, $date);
    }

    /**
     * Post the relief against the given expense account.
     *
     * @throws InvalidValueException when the amount is not positive
     */
    private function relieve(
        StudentRecord $enrollment,
        float $amount,
        string $expensePurpose,
        string $description,
        ?Model $source,
        ?User $actor,
        ?CarbonInterface $date,
        ?int $schoolId = null,
    ): LedgerTransaction {
        if ($amount <= 0) {
            throw new InvalidValueException('The amount must be more than nothing.');
        }

        $schoolId ??= $enrollment->school_id;

        return $this->post->post(
            description: $description,
            lines: [
                [
                    'account' => $this->chart->account($expensePurpose, $schoolId),
                    'debit' => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo' => $description,
                ],
                [
                    'account' => $this->chart->account('fees_receivable', $schoolId),
                    'credit' => $amount,
                    'student_record_id' => $enrollment->id,
                    'memo' => $description,
                ],
            ],
            date: $date,
            source: $source,
            actor: $actor,
        );
    }
}
