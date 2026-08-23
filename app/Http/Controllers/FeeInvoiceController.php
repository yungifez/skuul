<?php

namespace App\Http\Controllers;

use App\Actions\Finance\ReceivePayment;
use App\Exceptions\InvalidValueException;
use App\Http\Requests\PayFeeInvoiceRequest;
use App\Http\Requests\StoreFeeInvoiceRequest;
use App\Http\Requests\UpdateFeeInvoiceRequest;
use App\Models\FeeInvoice;
use App\Services\Fee\FeeInvoiceService;
use App\Services\Finance\PaymentChannelRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FeeInvoiceController extends Controller
{
    public FeeInvoiceService $feeInvoiceService;

    public function __construct(FeeInvoiceService $feeInvoiceService)
    {
        $this->feeInvoiceService = $feeInvoiceService;
        $this->authorizeResource(FeeInvoice::class, 'fee_invoice');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.fee.fee-invoice.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.fee.fee-invoice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeInvoiceRequest $request): RedirectResponse
    {
        $this->feeInvoiceService->storeFeeInvoice($request->validated());

        return back()->with('success', 'Fee Invoice Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeInvoice $feeInvoice): View
    {
        return view('pages.fee.fee-invoice.show', compact('feeInvoice'));
    }

    /**
     * Display the specified resource.
     */
    public function print(FeeInvoice $feeInvoice): Response
    {
        $this->authorize('view', $feeInvoice);

        return $this->feeInvoiceService->printFeeInvoice($feeInvoice->name, 'pages.fee.fee-invoice.print', compact('feeInvoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeInvoice $feeInvoice): View
    {
        return view('pages.fee.fee-invoice.edit', compact('feeInvoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeInvoiceRequest $request, FeeInvoice $feeInvoice): RedirectResponse
    {
        $this->feeInvoiceService->updateFeeInvoice($feeInvoice, $request->validated());

        return back()->with('success', 'Fee Invoice Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeInvoice $feeInvoice): RedirectResponse
    {
        $this->feeInvoiceService->deleteFeeInvoice($feeInvoice);

        return back()->with('success', 'Fee Invoice Deleted Successfully');
    }

    /**
     * Show the form for taking money against the invoice.
     */
    public function payView(FeeInvoice $feeInvoice, PaymentChannelRegistry $channels): View
    {
        $this->authorize('update', $feeInvoice);

        $feeInvoice->loadMissing(['user.studentRecord', 'feeInvoiceRecords.fee', 'feeInvoiceRecords.allocations']);

        return view('pages.fee.fee-invoice.pay', [
            'feeInvoice' => $feeInvoice,
            'channels' => $channels->all(),
        ]);
    }

    /**
     * Take money against the invoice and say which fees it settles.
     *
     * The office either names the fees itself or lets the application clear
     * the oldest ones first. Anything left over stays as credit for the
     * student, so no money is lost and no line is overpaid.
     */
    public function pay(FeeInvoice $feeInvoice, PayFeeInvoiceRequest $request, ReceivePayment $receive): RedirectResponse
    {
        $this->authorize('update', $feeInvoice);

        $enrollment = $feeInvoice->user?->studentRecord;

        if ($enrollment === null) {
            throw new InvalidValueException('This invoice does not belong to an enrolled student.');
        }

        $payment = $receive->receive(
            enrollment: $enrollment,
            amount: $request->minorAmount(),
            method: $request->validated('method'),
            allocations: $request->allocationPlan(),
            onlyInvoice: $feeInvoice->id,
            reference: $request->validated('reference'),
            note: $request->validated('note'),
            receivedOn: $request->validated('received_on') === null ? null : now()->parse($request->validated('received_on')),
            source: $feeInvoice,
        );

        $credit = $payment->unallocated();
        $message = $credit->isPositive()
            ? 'Payment recorded. '.$credit->formatToLocale(app()->getLocale()).' is held as credit for this student.'
            : 'Payment recorded.';

        return redirect()->route('fee-invoices.show', $feeInvoice->id)->with('success', $message);
    }
}
