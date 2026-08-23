<?php

namespace App\Services\Fee;

use App\Actions\Finance\ChargeStudent;
use App\Exceptions\InvalidValueException;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\User;
use App\Services\Print\PrintService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FeeInvoiceService
{
    public function __construct(private ChargeStudent $chargeStudent) {}

    /**
     * Store a new Fee Invoice.
     *
     * @param  array  $records
     */
    public function storeFeeInvoice($records)
    {
        $invalidFees = Fee::whereIn('id', collect($records['records'])->pluck('fee_id'))->whereRelation('feeCategory', 'school_id', '!=', current_school_id())->get();

        if ($invalidFees->isNotEmpty()) {
            throw new InvalidValueException('Some Fees Are Not From This School', 1);
        }
        $invalidUsers = User::whereIn('id', collect($records['users']))->get()->contains(function ($user) {
            if (current_school_id() != current_school_id()) {
                return true;
            }

            if (!$user->studentRecord()->exists()) {
                return true;
            }
        });

        if ($invalidUsers == true) {
            throw new InvalidValueException('Some Users Are Invalid', 1);
        }

        DB::transaction(function () use ($records) {
            foreach ($records['users'] as $user) {
                $feeInvoice = FeeInvoice::create([
                    'issue_date' => $records['issue_date'],
                    'due_date' => $records['due_date'],
                    'note' => $records['note'] ?? null,
                    'name' => $this->generateInvoiceNumber(),
                    'user_id' => $user,
                ]);

                $feeInvoice->feeInvoiceRecords()->createMany($records['records']);

                $this->chargeTheStudent($feeInvoice);
            }
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
        $enrollment = $feeInvoice->user?->studentRecord;

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

        $this->chargeStudent->charge(
            enrollment: $enrollment,
            amount: $amount,
            description: "Invoice $feeInvoice->name",
            source: $feeInvoice,
        );
    }

    /**
     * Update a fee invoice.
     *
     *
     * @return void
     */
    public function updateFeeInvoice(FeeInvoice $feeInvoice, $records)
    {
        $feeInvoice->update([
            'issue_date' => $records['issue_date'],
            'due_date' => $records['due_date'],
            'note' => $records['note'] ?? null,
        ]);

        return $feeInvoice;
    }

    /**
     * Generate a new fee invoice name.
     *
     *
     * @return string
     */
    public function generateInvoiceNumber(?int $schoolId = null)
    {
        $schoolInitials = (School::find($schoolId) ?? current_school())->initials;
        $schoolInitials != null && $schoolInitials .= '-';

        do {
            $invoiceNumber = "Fee-Invoice-$schoolInitials".\mt_rand(100_000_000, 999_999_999);
            if (FeeInvoice::where('name', $invoiceNumber)->count() <= 0) {
                $uniqueAdmissionNumberFound = true;
            } else {
                $uniqueAdmissionNumberFound = false;
            }
        } while ($uniqueAdmissionNumberFound == false);

        return $invoiceNumber;
    }

    /**
     * Print Fee Invoice.
     *
     *
     * @return Response
     */
    public function printFeeInvoice(string $name, string $view, array $data)
    {
        return PrintService::download($view, $data, $name);
    }

    /**
     * Delete a fee invoice.
     *
     *
     * @return void
     */
    public function deleteFeeInvoice(FeeInvoice $feeInvoice)
    {
        $feeInvoice->delete();
    }
}
