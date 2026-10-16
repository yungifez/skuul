<?php

namespace App\Http\Controllers;

use App\Models\CashDeposit;
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
}
