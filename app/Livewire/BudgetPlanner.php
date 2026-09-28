<?php

namespace App\Livewire;

use App\Actions\Finance\SetBudget;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Budget;
use App\Models\LedgerAccount;
use App\Models\Program;
use App\Services\Finance\BudgetVersusActual;
use App\Services\Finance\ChartOfAccounts;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The plans of one cycle beside what the books say happened.
 *
 * Writing a plan for the same account and stretch of the year again revises
 * it, so the office never ends up with two plans counting the same money.
 */
class BudgetPlanner extends Component
{
    use DispatchesStatusNotifications;

    #[Url(as: 'academic_year_id', except: '')]
    public string $academicYearId = '';

    public string $ledgerAccountId = '';

    public string $amount = '';

    public string $academicPeriodId = '';

    public string $programId = '';

    public string $fund = '';

    public string $note = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Budget::class);
    }

    public function updatedAcademicYearId(): void
    {
        $this->reset('academicPeriodId');
        $this->resetValidation();
    }

    /**
     * Fill the form with a plan, so revising it starts from what it says.
     */
    public function revise(int $budgetId): void
    {
        Gate::authorize('create', Budget::class);

        $budget = Budget::query()->inSchool()->findOrFail($budgetId);

        $this->academicYearId = (string) $budget->academic_year_id;
        $this->ledgerAccountId = (string) $budget->ledger_account_id;
        $this->amount = number_format($budget->amount, 2, '.', '');
        $this->academicPeriodId = (string) $budget->academic_period_id;
        $this->programId = (string) $budget->program_id;
        $this->fund = (string) $budget->fund;
        $this->note = (string) $budget->note;
        $this->resetValidation();
    }

    public function save(SetBudget $setBudget): void
    {
        Gate::authorize('create', Budget::class);

        $academicYear = $this->academicYear() ?? abort(404);
        $this->fund = trim($this->fund);
        $this->note = trim($this->note);

        $this->validate([
            'ledgerAccountId' => ['required', 'integer', Rule::exists((new LedgerAccount)->getTable(), 'id')->where('school_id', current_school_id())],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:1000000000'],
            'academicPeriodId' => ['nullable', 'integer', Rule::exists((new AcademicPeriod)->getTable(), 'id')->where('school_id', current_school_id())->where('academic_year_id', $academicYear->id)],
            'programId' => ['nullable', 'integer', Rule::exists((new Program)->getTable(), 'id')->where('school_id', current_school_id())],
            'fund' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [
            'ledgerAccountId.required' => 'Say which account the plan is about.',
            'amount.required' => 'Say how much the account is allowed.',
        ], [
            'ledgerAccountId' => 'account',
            'academicPeriodId' => 'term',
            'programId' => 'programme',
        ]);

        try {
            $setBudget->set(
                academicYear: $academicYear,
                account: LedgerAccount::query()->inSchool()->findOrFail((int) $this->ledgerAccountId),
                amount: (float) $this->amount,
                academicPeriod: $this->academicPeriodId === '' ? null : AcademicPeriod::query()->inSchool()->findOrFail((int) $this->academicPeriodId),
                program: $this->programId === '' ? null : Program::query()->inSchool()->findOrFail((int) $this->programId),
                fund: $this->fund,
                note: $this->note,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('amount', $exception->getMessage());

            return;
        }

        $this->reset('ledgerAccountId', 'amount', 'academicPeriodId', 'programId', 'fund', 'note');
        $this->notify('The budget was saved.');
    }

    public function remove(int $budgetId, SetBudget $setBudget): void
    {
        $budget = Budget::query()->inSchool()->findOrFail($budgetId);
        Gate::authorize('delete', $budget);

        $setBudget->remove($budget, auth()->user());
        $this->notify('The budget was removed.');
    }

    public function render(BudgetVersusActual $comparison, ChartOfAccounts $chart): View
    {
        $academicYear = $this->academicYear();

        return view('livewire.budget-planner', [
            'academicYear' => $academicYear,
            'academicYears' => AcademicYear::query()->inSchool()->orderByDesc('id')->get(),
            'rows' => $academicYear === null ? collect() : $comparison->forCycle($academicYear),
            'periods' => $academicYear === null
                ? collect()
                : AcademicPeriod::query()->inSchool()->where('academic_year_id', $academicYear->id)->orderBy('position')->get(['id', 'name']),
            'accounts' => LedgerAccount::query()->inSchool()->orderBy('code')->get()->whenEmpty(
                fn () => $chart->ensureFor(current_school_id())->values(),
            ),
            'programs' => Program::query()->inSchool()->orderBy('name')->get(['id', 'name']),
            'canWrite' => Gate::allows('create', Budget::class),
        ]);
    }

    /**
     * Get the cycle the screen is showing.
     */
    private function academicYear(): ?AcademicYear
    {
        if (ctype_digit($this->academicYearId)) {
            return AcademicYear::query()->inSchool()->find((int) $this->academicYearId);
        }

        return AcademicYear::query()->inSchool()->find(current_academic_year_id())
            ?? AcademicYear::query()->inSchool()->orderByDesc('id')->first();
    }
}
