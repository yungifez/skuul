<?php

namespace App\Http\Controllers;

use App\Actions\Finance\RecordCashDeposit;
use App\Http\Requests\StoreCashDepositRequest;
use App\Models\CashDeposit;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CashDepositController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->can('read cash deposit') === true, 403);

        return view('pages.fee.cash-deposits.index', [
            'deposits' => CashDeposit::query()->inSchool()->with('financialPeriod')->orderByDesc('deposit_date')->orderByDesc('id')->paginate(25),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->can('create cash deposit') === true, 403);

        return view('pages.fee.cash-deposits.create');
    }

    public function store(StoreCashDepositRequest $request, RecordCashDeposit $record): RedirectResponse
    {
        $data = $request->validated();
        $record->record(
            amount: (float) $data['amount'],
            date: Carbon::parse($data['deposit_date']),
            bankReference: $data['bank_reference'] ?? null,
            note: $data['note'] ?? null,
        );

        return redirect()->route('cash-deposits.index')->with('success', 'Cash deposit recorded.');
    }
}
