<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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

    public function create(): View
    {
        return view('pages.fee.expenses.create');
    }
}
