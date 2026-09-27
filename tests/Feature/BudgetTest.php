<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Finance\PostLedgerTransaction;
use App\Actions\Finance\ReverseLedgerTransaction;
use App\Actions\Finance\SetBudget;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Livewire\BudgetPlanner;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\Budget;
use App\Models\FinancialPeriod;
use App\Models\LedgerTransaction;
use App\Models\Program;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Finance\BudgetVersusActual;
use App\Services\Finance\ChartOfAccounts;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * What a campus plans to spend, beside what the books say it did.
 */
class BudgetTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_budget_is_revised_rather_than_written_twice(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');

        app(SetBudget::class)->set($cycle, $account, 5_000);
        app(SetBudget::class)->set($cycle, $account, 7_500);

        $this->assertSame(1, Budget::where('academic_year_id', $cycle->id)->count());
        $this->assertSame(7500.0, Budget::first()->amount);
    }

    public function test_the_same_account_can_be_planned_for_each_term(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');
        $first = AcademicPeriod::factory()->create(['school_id' => $cycle->school_id, 'academic_year_id' => $cycle->id]);
        $second = AcademicPeriod::factory()->create(['school_id' => $cycle->school_id, 'academic_year_id' => $cycle->id]);

        app(SetBudget::class)->set($cycle, $account, 1_000, $first);
        app(SetBudget::class)->set($cycle, $account, 2_000, $second);

        $this->assertSame(2, Budget::where('academic_year_id', $cycle->id)->count());
    }

    public function test_a_budget_refuses_an_account_from_another_campus(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $elsewhere = app(ChartOfAccounts::class)->account('operating_expenses', School::factory()->create());

        $this->expectException(InvalidValueException::class);

        app(SetBudget::class)->set($cycle, $elsewhere, 1_000);
    }

    public function test_a_budget_refuses_a_term_from_another_cycle(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $otherCycle = AcademicYear::factory()->create(['school_id' => $cycle->school_id]);
        $stray = AcademicPeriod::factory()->create(['school_id' => $cycle->school_id, 'academic_year_id' => $otherCycle->id]);

        $this->expectException(InvalidValueException::class);

        app(SetBudget::class)->set($cycle, app(ChartOfAccounts::class)->account('operating_expenses'), 500, $stray);
    }

    public function test_the_comparison_reads_what_happened_from_the_books(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $chart = app(ChartOfAccounts::class);
        app(SetBudget::class)->set($cycle, $chart->account('tuition_income'), 1_000);

        app(ChargeStudent::class)->charge($this->enrollment(), 400, 'Term one fees');

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(1000.0, $row->planned);
        $this->assertSame(400.0, $row->actual);
        $this->assertSame(-600.0, $row->difference());
        $this->assertFalse($row->isOverspent());
        $this->assertSame(40.0, $row->used());
    }

    public function test_the_comparison_says_when_a_plan_is_overspent(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $chart = app(ChartOfAccounts::class);
        app(SetBudget::class)->set($cycle, $chart->account('operating_expenses'), 100);

        $this->spend(250);

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(250.0, $row->actual);
        $this->assertTrue($row->isOverspent());
    }

    public function test_a_plan_narrowed_to_a_fund_only_counts_that_fund(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $chart = app(ChartOfAccounts::class);
        app(SetBudget::class)->set($cycle, $chart->account('operating_expenses'), 500, fund: 'library');

        $this->spend(120, 'library');
        $this->spend(300, 'building');

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(120.0, $row->actual);
    }

    public function test_a_plan_narrowed_to_a_programme_only_counts_that_programme(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $chart = app(ChartOfAccounts::class);
        $program = Program::create(['school_id' => $cycle->school_id, 'name' => 'Science club']);
        app(SetBudget::class)->set($cycle, $chart->account('operating_expenses'), 500, program: $program);

        $this->spend(80, null, $program->id);
        $this->spend(400);

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(80.0, $row->actual);
    }

    public function test_money_spent_outside_the_cycle_is_left_out(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        app(SetBudget::class)->set($cycle, app(ChartOfAccounts::class)->account('operating_expenses'), 500);

        $outsideDate = now()->subYears(3);
        FinancialPeriod::query()->create([
            'school_id' => $cycle->school_id,
            'name' => 'Historical finance period',
            'starts_on' => $outsideDate->copy()->startOfYear()->toDateString(),
            'ends_on' => $outsideDate->copy()->endOfYear()->toDateString(),
        ]);

        $this->spend(200, date: $outsideDate);

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(0.0, $row->actual);
    }

    public function test_setting_a_budget_is_written_to_the_audit_log(): void
    {
        $this->authorized_user([]);
        $budget = app(SetBudget::class)->set($this->cycle(), app(ChartOfAccounts::class)->account('operating_expenses'), 900);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::BudgetSet)->forSubject($budget)->first());
    }

    public function test_a_reversal_keeps_the_dimensions_of_what_it_undoes(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        app(SetBudget::class)->set($cycle, app(ChartOfAccounts::class)->account('operating_expenses'), 500, fund: 'library');
        $spend = $this->spend(150, 'library');

        app(ReverseLedgerTransaction::class)->reverse($spend, 'Paid from the wrong fund');

        $row = app(BudgetVersusActual::class)->forCycle($cycle)->sole();

        $this->assertSame(0.0, $row->actual);
    }

    public function test_an_unauthorized_user_cannot_read_budgets(): void
    {
        $this->unauthorized_user()->get(route('budgets.index'))->assertForbidden();
    }

    public function test_the_office_can_write_a_budget_from_the_screen(): void
    {
        $this->authorized_user(['read budget', 'manage budget']);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');

        $this->get(route('budgets.index', ['academic_year_id' => $cycle->id]))->assertOk()->assertSee($cycle->name);

        Livewire::withQueryParams(['academic_year_id' => $cycle->id])
            ->test(BudgetPlanner::class)
            ->set('ledgerAccountId', (string) $account->id)
            ->set('amount', '1250.50')
            ->set('fund', ' library ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee($account->name);

        $this->assertDatabaseHas('budgets', [
            'school_id' => $cycle->school_id,
            'academic_year_id' => $cycle->id,
            'ledger_account_id' => $account->id,
            'fund' => 'library',
        ]);
    }

    public function test_reading_budgets_does_not_allow_writing_them(): void
    {
        $this->authorized_user(['read budget']);
        $cycle = $this->cycle();
        $budget = app(SetBudget::class)->set($cycle, app(ChartOfAccounts::class)->account('operating_expenses'), 100);

        Livewire::test(BudgetPlanner::class)
            ->set('ledgerAccountId', (string) app(ChartOfAccounts::class)->account('operating_expenses')->id)
            ->set('amount', '500')
            ->call('save')
            ->assertForbidden();

        Livewire::test(BudgetPlanner::class)->call('remove', $budget->id)->assertForbidden();

        $this->assertSame(100.0, $budget->fresh()->amount);
    }

    public function test_a_fund_in_other_capitals_revises_the_same_plan(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');

        app(SetBudget::class)->set($cycle, $account, 1_000, fund: 'Library fund');
        app(SetBudget::class)->set($cycle, $account, 1_500, fund: 'library FUND ');

        $this->assertSame(1, Budget::count());
        $this->assertSame(1500.0, Budget::sole()->amount);
        $this->assertSame('Library fund', Budget::sole()->fund);
    }

    public function test_saving_the_same_plan_again_writes_nothing_new(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');

        app(SetBudget::class)->set($cycle, $account, 1_000);
        app(SetBudget::class)->set($cycle, $account, 1_000);

        $this->assertSame(1, AuditEvent::ofAction(AuditAction::BudgetSet)->count());
    }

    public function test_removing_a_plan_is_written_to_the_audit_log(): void
    {
        $this->authorized_user(['read budget', 'manage budget']);
        $budget = app(SetBudget::class)->set($this->cycle(), app(ChartOfAccounts::class)->account('operating_expenses'), 800);

        Livewire::test(BudgetPlanner::class)->call('remove', $budget->id)->assertHasNoErrors();

        $this->assertNull($budget->fresh());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::BudgetRemoved)->first());
    }

    public function test_a_plan_of_another_campus_is_never_touched(): void
    {
        $this->authorized_user(['read budget', 'manage budget']);
        $elsewhere = School::factory()->create();
        $theirCycle = AcademicYear::factory()->create(['school_id' => $elsewhere->id]);
        $theirAccount = app(ChartOfAccounts::class)->ensureFor($elsewhere->id)->first();
        $theirs = Budget::query()->create([
            'school_id' => $elsewhere->id,
            'academic_year_id' => $theirCycle->id,
            'ledger_account_id' => $theirAccount->id,
            'amount' => 900,
            'scope_hash' => Budget::hashFor($theirCycle->id, null, $theirAccount->id, null, null),
        ]);

        $planner = Livewire::test(BudgetPlanner::class);

        $this->assertThrows(fn () => $planner->call('remove', $theirs->id), ModelNotFoundException::class);
        $this->assertThrows(fn () => $planner->call('revise', $theirs->id), ModelNotFoundException::class);
        $this->assertNotNull($theirs->fresh());

        Livewire::withQueryParams(['academic_year_id' => $theirCycle->id])
            ->test(BudgetPlanner::class)
            ->set('ledgerAccountId', (string) $theirAccount->id)
            ->set('amount', '5')
            ->call('save')
            ->assertStatus(404);
    }

    public function test_revising_starts_from_what_the_plan_says(): void
    {
        $this->authorized_user(['read budget', 'manage budget']);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');
        $budget = app(SetBudget::class)->set($cycle, $account, 640, fund: 'Sports', note: 'Kit');

        Livewire::test(BudgetPlanner::class)
            ->call('revise', $budget->id)
            ->assertSet('ledgerAccountId', (string) $account->id)
            ->assertSet('amount', '640.00')
            ->assertSet('fund', 'Sports')
            ->set('amount', '700')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(700.0, $budget->fresh()->amount);
        $this->assertSame(1, Budget::count());
    }

    /**
     * Get a cycle in the working school that covers today.
     */
    private function cycle(): AcademicYear
    {
        return AcademicYear::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'starts_on' => now()->startOfYear()->toDateString(),
            'ends_on' => now()->endOfYear()->toDateString(),
        ]);
    }

    /**
     * Create an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Spend money out of the bank against an optional fund and programme.
     */
    private function spend(float $amount, ?string $fund = null, ?int $programId = null, mixed $date = null): LedgerTransaction
    {
        $chart = app(ChartOfAccounts::class);

        return app(PostLedgerTransaction::class)->post(
            description: 'Something the school bought',
            lines: [
                [
                    'account' => $chart->account('operating_expenses'),
                    'debit' => $amount,
                    'fund' => $fund,
                    'program_id' => $programId,
                ],
                [
                    'account' => $chart->account('bank'),
                    'credit' => $amount,
                ],
            ],
            date: $date,
        );
    }
}
