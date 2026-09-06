<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeInvoiceRecordRequest;
use App\Http\Requests\UpdateFeeInvoiceRecordRequest;
use App\Models\FeeInvoiceRecord;
use App\Services\Fee\FeeInvoiceRecordService;
use Illuminate\Http\RedirectResponse;

class FeeInvoiceRecordController extends Controller
{
    public FeeInvoiceRecordService $feeInvoiceRecordService;

    public function __construct(FeeInvoiceRecordService $feeInvoiceRecordService)
    {
        $this->feeInvoiceRecordService = $feeInvoiceRecordService;

        $this->authorizeResource(FeeInvoiceRecord::class, 'fee_invoice_record');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeInvoiceRecordRequest $request): RedirectResponse
    {
        $this->feeInvoiceRecordService->storeFeeInvoiceRecord($request->validated());

        return back()->with('success', 'Fee added to Fee Invoice Successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeInvoiceRecordRequest $request, FeeInvoiceRecord $feeInvoiceRecord): RedirectResponse
    {
        $this->feeInvoiceRecordService->updateFeeInvoiceRecord($feeInvoiceRecord, $request->validated());

        return back()->with('success', 'Fee Details Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeInvoiceRecord $feeInvoiceRecord): RedirectResponse
    {
        $this->feeInvoiceRecordService->deleteFeeInvoiceRecord($feeInvoiceRecord);

        return back()->with('success', 'Fee Removed From Fee Invoice Successfully');
    }
}
