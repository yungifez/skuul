<?php

namespace App\Actions\Finance;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Put an invoice raised before the books existed on the learner's account.
 *
 * Invoices from before the upgrade carry no ledger entry. Money taken on them
 * still reaches the books, so without this the account says one thing and its
 * bills another. The charge is what the invoice still owes once the payments
 * the books never saw are taken off, so nothing is counted twice.
 */
class BringInvoiceIntoTheBooks
{
    public function __construct(
        private ChargeStudent $chargeStudent,
        private RecordAuditEvent $auditor,
    ) {}

    /**
     * Bring every open invoice of the learner at one campus into the books.
     */
    public function forLearner(StudentRecord $enrollment, int $schoolId, ?User $actor = null): void
    {
        FeeInvoice::query()
            ->where('student_record_id', $enrollment->id)
            ->where('school_id', $schoolId)
            ->whereNull('ledger_transaction_id')
            ->get()
            ->each(fn (FeeInvoice $invoice) => $this->bring($invoice, $actor));
    }

    /**
     * Bring one invoice into the books, once.
     *
     * Returns the amount charged in minor units, or 0 when nothing was due.
     */
    public function bring(FeeInvoice $invoice, ?User $actor = null): int
    {
        return DB::transaction(function () use ($invoice, $actor): int {
            $invoice = FeeInvoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $enrollment = $invoice->studentRecord;

            if ($invoice->ledger_transaction_id !== null || $enrollment === null) {
                return 0;
            }

            $records = FeeInvoiceRecord::query()->where('fee_invoice_id', $invoice->id);
            $charged = (int) $records->sum('amount') + (int) $records->clone()->sum('fine') - (int) $records->clone()->sum('waiver');
            // Payments the books already hold reduced the account when they
            // were taken. Only the ones from before the books are taken off.
            $paidOutsideTheBooks = (int) $invoice->allocations()
                ->whereHas('studentPayment', fn ($payment) => $payment->whereNull('ledger_transaction_id'))
                ->sum('amount');
            $owed = $charged - $paidOutsideTheBooks;

            if ($owed <= 0) {
                return 0;
            }

            $transaction = $this->chargeStudent->charge(
                enrollment: $enrollment,
                amount: round($owed / 100, 2),
                description: "Invoice $invoice->name brought into the books",
                source: $invoice,
                actor: $actor,
                schoolId: $invoice->school_id,
            );

            $invoice->forceFill(['ledger_transaction_id' => $transaction->id])->save();

            $this->auditor->record(
                AuditAction::InvoiceBroughtIntoBooks,
                $invoice,
                ['amount' => $owed],
                $actor,
                $invoice->school_id,
            );

            return $owed;
        });
    }
}
