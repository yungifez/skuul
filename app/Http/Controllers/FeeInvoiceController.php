<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FeeInvoice;
use App\Models\FinancialPeriod;
use App\Models\StudentPayment;
use App\Services\Fee\FeeInvoiceService;
use App\Services\Finance\FinancialPeriodResolver;
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
    public function index(FinancialPeriodResolver $periods): View
    {
        $financialPeriods = FinancialPeriod::query()->inSchool()->orderByDesc('starts_on')->get();
        $period = request()->integer('financial_period_id') > 0
            ? $financialPeriods->firstWhere('id', request()->integer('financial_period_id'))
            : $periods->currentOpen(current_school_id());

        $invoices = $period === null
            ? collect()
            : FeeInvoice::query()->ofSchool()->where('financial_period_id', $period->id)
                ->with(['feeInvoiceRecords.allocations', 'allocations'])->get();

        $outstanding = $invoices->sum(fn (FeeInvoice $invoice): int => max($invoice->balance->getMinorAmount()->toInt(), 0));
        $overdue = $invoices->filter(fn (FeeInvoice $invoice): bool => $invoice->balance->isPositive() && $invoice->due_date->lt(today()))->count();
        $received = $period === null ? 0 : (int) StudentPayment::query()->inSchool()->where('financial_period_id', $period->id)->sum('amount');
        $spent = $period === null ? 0 : (float) Expense::query()->inSchool()->where('financial_period_id', $period->id)->sum('amount');

        return view('pages.fee.fee-invoice.index', [
            'period' => $period,
            'summary' => [
                'outstanding' => $outstanding / 100,
                'overdue' => $overdue,
                'received' => $received / 100,
                'spent' => $spent,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.fee.fee-invoice.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeInvoice $feeInvoice): View
    {
        return view('pages.fee.fee-invoice.show', compact('feeInvoice'));
    }

    /**
     * Print the invoice, or its receipt once it is paid in full.
     */
    public function print(FeeInvoice $feeInvoice): Response
    {
        $this->authorize('view', $feeInvoice);

        $feeInvoice->loadMissing([
            'user',
            'studentRecord.academicCycleSection.academicLevel',
            'feeInvoiceRecords.fee',
            'feeInvoiceRecords.allocations',
            'allocations.studentPayment',
        ]);

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
     * Show the form for taking money against the invoice.
     */
    public function payView(FeeInvoice $feeInvoice): View
    {
        $this->authorize('update', $feeInvoice);

        return view('pages.fee.fee-invoice.pay', ['feeInvoice' => $feeInvoice]);
    }
}
