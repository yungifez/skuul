<?php

namespace App\Http\Controllers;

use App\Actions\Finance\SetBudget;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Services\Finance\BudgetVersusActual;
use App\Services\Finance\ChartOfAccounts;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * What a campus plans to spend and take in, beside what it actually did.
 */
class BudgetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Budget::class, 'budget');
    }

    /**
     * Show the plans of one cycle and how they are running.
     */
    public function index(BudgetVersusActual $comparison, ChartOfAccounts $chart): View
    {
        $academicYear = $this->cycleInView();

        return view('pages.fee.budget.index', [
            'academicYear' => $academicYear,
            'academicYears' => AcademicYear::inSchool()->orderByDesc('id')->get(),
            'rows' => $academicYear === null ? collect() : $comparison->forCycle($academicYear),
            'periods' => $academicYear === null
                ? collect()
                : AcademicPeriod::inSchool()->where('academic_year_id', $academicYear->id)->orderBy('position')->get(),
            'accounts' => LedgerAccount::inSchool()->orderBy('code')->get()->whenEmpty(
                fn () => $chart->ensureFor(current_school_id())->values(),
            ),
            'programs' => Program::inSchool()->orderBy('name')->get(),
        ]);
    }

    /**
     * Write or revise one plan.
     */
    public function store(StoreBudgetRequest $request, SetBudget $setBudget): RedirectResponse
    {
        $academicYear = AcademicYear::inSchool()->findOrFail($request->validated('academic_year_id'));

        $setBudget->set(
            academicYear: $academicYear,
            account: LedgerAccount::inSchool()->findOrFail($request->validated('ledger_account_id')),
            amount: (float) $request->validated('amount'),
            academicPeriod: $request->validated('academic_period_id') === null
                ? null
                : AcademicPeriod::inSchool()->findOrFail($request->validated('academic_period_id')),
            program: $request->validated('program_id') === null
                ? null
                : Program::inSchool()->findOrFail($request->validated('program_id')),
            fund: $request->validated('fund'),
            note: $request->validated('note'),
        );

        return redirect()
            ->route('budgets.index', ['academic_year_id' => $academicYear->id])
            ->with('success', 'The budget was saved.');
    }

    /**
     * Drop one plan.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        $academicYearId = $budget->academic_year_id;
        $budget->delete();

        return redirect()
            ->route('budgets.index', ['academic_year_id' => $academicYearId])
            ->with('success', 'The budget was removed.');
    }

    /**
     * Get the cycle the screen is showing.
     */
    private function cycleInView(): ?AcademicYear
    {
        $asked = request()->integer('academic_year_id');

        if ($asked > 0) {
            return AcademicYear::inSchool()->find($asked);
        }

        return AcademicYear::inSchool()->find(current_academic_year_id())
            ?? AcademicYear::inSchool()->orderByDesc('id')->first();
    }
}
