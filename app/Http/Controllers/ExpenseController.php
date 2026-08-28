<?php

namespace App\Http\Controllers;

use App\Actions\Finance\RecordExpense;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Services\Finance\PaymentChannelRegistry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Expense::class, 'expense');
    }

    public function index(): View
    {
        return view('pages.fee.expenses.index', [
            'expenses' => Expense::query()->inSchool()->with(['account', 'financialPeriod', 'recordedBy'])
                ->orderByDesc('expense_date')->orderByDesc('id')->paginate(25),
        ]);
    }

    public function create(PaymentChannelRegistry $channels): View
    {
        return view('pages.fee.expenses.create', [
            'accounts' => LedgerAccount::query()->inSchool()->where('type', 'expense')->where('is_active', true)->orderBy('code')->get(),
            'programs' => Program::query()->inSchool()->orderBy('name')->get(),
            'channels' => $channels->all(),
        ]);
    }

    public function store(StoreExpenseRequest $request, RecordExpense $record): RedirectResponse
    {
        $data = $request->validated();

        $record->record(
            account: LedgerAccount::query()->inSchool()->findOrFail($data['ledger_account_id']),
            amount: (float) $data['amount'],
            description: $data['description'],
            method: $data['method'],
            date: Carbon::parse($data['expense_date']),
            vendor: $data['vendor'] ?? null,
            reference: $data['reference'] ?? null,
            note: $data['note'] ?? null,
            program: $data['program_id'] === null ? null : Program::query()->inSchool()->findOrFail($data['program_id']),
            fund: $data['fund'] ?? null,
        );

        return redirect()->route('expenses.index')->with('success', 'Expense recorded.');
    }
}
