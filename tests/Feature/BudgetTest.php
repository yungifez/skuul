<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Finance\PostLedgerTransaction;
use App\Actions\Finance\ReverseLedgerTransaction;
use App\Actions\Finance\SetBudget;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\Budget;
use App\Models\LedgerTransaction;
use App\Models\Program;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Finance\BudgetVersusActual;
use App\Services\Finance\ChartOfAccounts;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->spend(200, date: now()->subYears(3));

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
        $actor = $this->authorized_user(['read budget', 'manage budget']);
        $cycle = $this->cycle();
        $account = app(ChartOfAccounts::class)->account('operating_expenses');

        $actor->get(route('budgets.index'))->assertOk()->assertSee('Budget against actual');

        $actor->post(route('budgets.store'), [
            'academic_year_id' => $cycle->id,
            'ledger_account_id' => $account->id,
            'amount' => 1250.50,
            'fund' => 'library',
        ])->assertRedirect(route('budgets.index', ['academic_year_id' => $cycle->id]));

        $this->assertDatabaseHas('budgets', [
            'school_id' => $cycle->school_id,
            'ledger_account_id' => $account->id,
            'fund' => 'library',
        ]);
    }

    public function test_reading_budgets_does_not_allow_writing_them(): void
    {
        $actor = $this->authorized_user(['read budget']);
        $cycle = $this->cycle();

        $actor->post(route('budgets.store'), [
            'academic_year_id' => $cycle->id,
            'ledger_account_id' => app(ChartOfAccounts::class)->account('operating_expenses')->id,
            'amount' => 100,
        ])->assertForbidden();

        $this->assertSame(0, Budget::count());
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
