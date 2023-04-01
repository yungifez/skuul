<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeInvoiceRequest;
use App\Http\Requests\UpdateFeeInvoiceRequest;
use App\Models\FeeInvoice;
use App\Services\Fee\FeeInvoiceService;
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

    public function payView(FeeInvoice $feeInvoice): View
    {
        $this->authorize('update', $feeInvoice);

        return view('pages.fee.fee-invoice.pay', compact('feeInvoice'));
    }
}
