<?php

namespace App\Services\Fee;

use App\Actions\Finance\ChargeStudent;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceBatch;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Finance\FinancialPeriodResolver;
use App\Services\Print\PrintService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeeInvoiceService
{
    public function __construct(
        private ChargeStudent $chargeStudent,
        private FinancialPeriodResolver $periods,
    ) {}

    /**
     * Store a new Fee Invoice.
     *
     * @param  array<string, mixed>  $records
     */
    public function storeFeeInvoice(array $records): void
    {
        $schoolId = current_school_id();
        $idempotencyKey = (string) ($records['idempotency_key'] ?? Str::uuid());

        Cache::lock("fee-invoice-batch:$schoolId:$idempotencyKey", 120)->block(10, function () use ($records, $schoolId, $idempotencyKey): void {
            if (FeeInvoiceBatch::query()
                ->inSchool()
                ->where('idempotency_key', $idempotencyKey)
                ->exists()) {
                return;
            }

            $feeIds = collect($records['records'])->pluck('fee_id')->map(fn (mixed $id): int => (int) $id)->unique();
            $fees = Fee::query()
                ->whereIn('id', $feeIds)
                ->whereRelation('feeCategory', 'school_id', $schoolId)
                ->get();

            if ($fees->count() !== $feeIds->count()) {
                throw new InvalidValueException('Some fees are not from this school.');
            }

            $enrollmentIds = collect($records['student_records'] ?? [])
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values();

            if ($enrollmentIds->isEmpty()) {
                throw new InvalidValueException('Add at least one enrolled student.');
            }

            $enrollments = StudentRecord::query()
                ->inSchool()
                ->whereIn('id', $enrollmentIds)
                ->where('status', EnrollmentStatus::Active)
                ->with('user')
                ->get()
                ->keyBy('id');

            if ($enrollments->count() !== $enrollmentIds->count()) {
                throw new InvalidValueException('Some selected students are not active in this school.');
            }

            $period = $this->periods->openFor($schoolId, $records['issue_date']);

            DB::transaction(function () use ($records, $enrollments, $period, $schoolId, $idempotencyKey): void {
                FeeInvoiceBatch::create([
                    'school_id' => $schoolId,
                    'idempotency_key' => $idempotencyKey,
                ]);

                foreach ($enrollments as $enrollment) {
                    $feeInvoice = FeeInvoice::create([
                        'issue_date' => $records['issue_date'],
                        'due_date' => $records['due_date'],
                        'note' => $records['note'] ?? null,
                        'name' => $this->generateInvoiceNumber($enrollment->school_id),
                        'user_id' => $enrollment->user_id,
                        'school_id' => $enrollment->school_id,
                        'student_record_id' => $enrollment->id,
                        'financial_period_id' => $period->id,
                    ]);

                    $feeInvoice->feeInvoiceRecords()->createMany($records['records']);

                    $this->chargeTheStudent($feeInvoice);
                }
            });
        });
    }

    /**
     * Put the invoice on the student's account in the books.
     *
     * The invoice screens still show their own totals, but what a student
     * owes is answered by the ledger, which cannot drift.
     */
    private function chargeTheStudent(FeeInvoice $feeInvoice): void
    {
        $enrollment = $feeInvoice->studentRecord;

        if ($enrollment === null) {
            return;
        }

        // Read the raw columns: they hold minor units, and the books hold
        // major units.
        $records = $feeInvoice->feeInvoiceRecords();
        $minor = (int) $records->sum('amount') + (int) $records->clone()->sum('fine') - (int) $records->clone()->sum('waiver');
        $amount = round($minor / 100, 2);

        if ($amount <= 0) {
            return;
        }

        $transaction = $this->chargeStudent->charge(
            enrollment: $enrollment,
            amount: $amount,
            description: "Invoice $feeInvoice->name",
            source: $feeInvoice,
            period: $feeInvoice->financialPeriod,
        );

        $feeInvoice->update(['ledger_transaction_id' => $transaction->id]);
    }

    /**
     * Update a fee invoice.
     * The issue date and lines are part of the posted record. Correct them with
     * a reversal and a replacement invoice instead of changing history.
     */
    public function updateFeeInvoice(FeeInvoice $feeInvoice, array $records): FeeInvoice
    {
        if ($records['issue_date'] !== $feeInvoice->issue_date->format('Y-m-d')) {
            throw new InvalidValueException('The issue date cannot change after an invoice is posted.');
        }

        $feeInvoice->update([
            'due_date' => $records['due_date'],
            'note' => $records['note'] ?? null,
        ]);

        return $feeInvoice;
    }

    /**
     * Generate a new fee invoice name.
     */
    public function generateInvoiceNumber(?int $schoolId = null): string
    {
        $schoolInitials = (School::find($schoolId) ?? current_school())?->initials;
        $schoolInitials = $schoolInitials === null ? '' : $schoolInitials.'-';

        do {
            $invoiceNumber = "Fee-Invoice-$schoolInitials".\mt_rand(100_000_000, 999_999_999);
        } while (FeeInvoice::withTrashed()
            ->where('school_id', $schoolId ?? current_school_id())
            ->where('name', $invoiceNumber)
            ->exists());

        return $invoiceNumber;
    }

    /**
     * Print Fee Invoice.
     *
     * @return Response
     */
    public function printFeeInvoice(string $name, string $view, array $data)
    {
        return PrintService::page($view, $data);
    }

    /**
     * Delete a fee invoice.
     * Posted invoices stay in the books. A correction must be a reversal or a
     * replacement, never a delete that hides the source entry.
     */
    public function deleteFeeInvoice(FeeInvoice $feeInvoice): void
    {
        $feeInvoice->loadMissing(['ledgerTransaction', 'allocations']);

        if ($feeInvoice->ledgerTransaction !== null || $feeInvoice->allocations->isNotEmpty()) {
            throw new InvalidValueException('Posted invoices cannot be deleted. Reverse or correct the invoice instead.');
        }

        $feeInvoice->delete();
    }
}
