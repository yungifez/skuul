<?php

namespace App\Services\Fee;

use Str;
use App\Models\Fee;
use App\Models\User;
use App\Models\School;
use App\Models\FeeInvoice;
use Illuminate\Support\Facades\DB;
use App\Services\Print\PrintService;
use App\Exceptions\InvalidValueException;

class FeeInvoiceService
{

    /**
     * Store a new Fee Invoice
     *
     * @param array $records
     */
    public function storeFeeInvoice($records)
    {   
        $invalidFees = Fee::whereIn('id',collect($records['records'])->pluck('fee_id'))->whereRelation('feeCategory', 'school_id', '!=',auth()->user()->school_id)->get();

        if ($invalidFees->isNotEmpty()) {
            throw new InvalidValueException("Some Fees Are Not From This School" ,  1);
        }
        $invalidUsers = User::whereIn('id',collect($records['users']))->get()->contains(function ($user)
        {
            if ($user->school_id != auth()->user()->school_id) {
                return true;
            }

            if (!$user->studentRecord()->exists()) {
                return true;
            }
        });

        if ($invalidUsers == true) {
            throw new InvalidValueException("Some Users Are Invalid" ,  1);
        }
     
        DB::transaction(function () use ($records)
        {
            foreach ($records['users'] as $user ) {
                $feeInvoice = FeeInvoice::create([
                    'issue_date' => $records['issue_date'],
                    'due_date' => $records['due_date'],
                    'note' => $records['note'] ?? null,
                    'name' => $this->generateInvoiceNumber(),
                    'user_id' => $user
                ]);

                $feeInvoice->feeInvoiceRecords()->createMany($records['records']);
            }
        });

        return;
    }

    public function generateInvoiceNumber($schoolId = null)
    {
        $schoolInitials = (School::find($schoolId) ?? auth()->user()->school)->initials;
        $schoolInitials != null && $schoolInitials .= '-';

        do {
            $invoiceNumber = "Fee-Invoice-$schoolInitials".\mt_rand('10000000', '99999999');
            if (FeeInvoice::where('name', $invoiceNumber)->count() <= 0) {
                $uniqueAdmissionNumberFound = true;
            } else {
                $uniqueAdmissionNumberFound = false;
            }
        } while ($uniqueAdmissionNumberFound == false);

        return $invoiceNumber;
    }
    
    public function printFeeInvoice(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView( $view, $data)->download($name.".pdf");
    }
}