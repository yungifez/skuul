<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayFeeInvoiceRequest;
use App\Http\Requests\StoreFeeInvoiceRecordRequest;
use App\Http\Requests\UpdateFeeInvoiceRecordRequest;
use App\Models\FeeInvoiceRecord;
use App\Services\Fee\FeeInvoiceRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class FeeInvoiceRecordController extends Controller
{
    public FeeInvoiceRecordService $feeInvoiceRecordService;

    public function __construct(FeeInvoiceRecordService $feeInvoiceRecordService)
    {
        $this->feeInvoiceRecordService = $feeInvoiceRecordService;

        $this->authorizeResource(FeeInvoiceRecord::class, 'fee_invoice_record');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        abort(404);
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
     * Display the specified resource.
     */
    public function show(FeeInvoiceRecord $feeInvoiceRecord): Response
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeInvoiceRecord $feeInvoiceRecord): Response
    {
        abort(404);
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

    public function pay(FeeInvoiceRecord $feeInvoiceRecord, PayFeeInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('update', [$feeInvoiceRecord]);
        $this->feeInvoiceRecordService->addPayment($feeInvoiceRecord, $request->validated());

        return back()->with('success', 'Payment added to Fee Successfully');
    }
}
