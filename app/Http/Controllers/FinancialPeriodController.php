<?php

namespace App\Http\Controllers;

use App\Actions\Finance\ChangeFinancialPeriodStatus;
use App\Enums\FinancialPeriodStatus;
use App\Http\Requests\StoreFinancialPeriodRequest;
use App\Models\FinancialPeriod;
use Illuminate\Http\RedirectResponse;

class FinancialPeriodController extends Controller
{
    public function store(StoreFinancialPeriodRequest $request): RedirectResponse
    {
        FinancialPeriod::create([
            ...$request->validated(),
            'school_id' => current_school_id(),
        ]);

        return back()->with('success', 'Financial period created.');
    }

    public function close(FinancialPeriod $financialPeriod, ChangeFinancialPeriodStatus $change): RedirectResponse
    {
        $this->authorizePeriod($financialPeriod);
        $change->change($financialPeriod, FinancialPeriodStatus::Closed, request('reason'));

        return back()->with('success', 'Financial period closed. New entries can no longer be posted to it.');
    }

    public function reopen(FinancialPeriod $financialPeriod, ChangeFinancialPeriodStatus $change): RedirectResponse
    {
        $this->authorizePeriod($financialPeriod);
        $change->change($financialPeriod, FinancialPeriodStatus::Open, request('reason'));

        return back()->with('success', 'Financial period reopened.');
    }

    private function authorizePeriod(FinancialPeriod $period): void
    {
        abort_unless(auth()->user()?->can('manage financial period') && $period->school_id === current_school_id(), 403);
    }
}
