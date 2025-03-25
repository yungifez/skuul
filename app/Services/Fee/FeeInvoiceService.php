<?php

namespace App\Services\Fee;

use App\Exceptions\InvalidValueException;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\User;
use App\Services\Print\PrintService;
use Illuminate\Support\Facades\DB;

class FeeInvoiceService
{
    /**
     * Store a new Fee Invoice.
     *
     * @param array $records
     */
    public function storeFeeInvoice($records)
    {
        $invalidFees = Fee::whereIn('id', collect($records['records'])->pluck('fee_id'))->whereRelation('feeCategory', 'school_id', '!=', auth()->user()->school_id)->get();

        if ($invalidFees->isNotEmpty()) {
            throw new InvalidValueException('Some Fees Are Not From This School', 1);
        }
        $invalidUsers = User::whereIn('id', collect($records['users']))->get()->contains(function ($user) {
            if ($user->school_id != auth()->user()->school_id) {
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
                    'due_date'   => $records['due_date'],
                    'note'       => $records['note'] ?? null,
                    'name'       => $this->generateInvoiceNumber(),
                    'user_id'    => $user,
                ]);

                $feeInvoice->feeInvoiceRecords()->createMany($records['records']);
            }
        });
    }

    /**
     * Update a fee invoice.
     *
     * @param FeeInvoice $feeInvoice
     * @param            $records
     *
     * @return void
     */
    public function updateFeeInvoice(FeeInvoice $feeInvoice, $records)
    {
        $feeInvoice->update([
            'issue_date' => $records['issue_date'],
            'due_date'   => $records['due_date'],
            'note'       => $records['note'] ?? null,
        ]);

        return $feeInvoice;
    }

    /**
     * Generate a new fee invoice name.
     *
     * @param int $schoolId
     *
     * @return string
     */
    public function generateInvoiceNumber(?int $schoolId = null)
    {
        $schoolInitials = (School::find($schoolId) ?? auth()->user()->school)->initials;
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
     * @param string $name
     * @param string $view
     * @param array  $data
     *
     * @return \Illuminate\Http\Response
     */
    public function printFeeInvoice(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($view, $data)->download($name.'.pdf');
    }

    /**
     * Delete a fee invoice.
     *
     * @param FeeInvoice $feeInvoice
     *
     * @return void
     */
    public function deleteFeeInvoice(FeeInvoice $feeInvoice)
    {
        $feeInvoice->delete();
    }
}
