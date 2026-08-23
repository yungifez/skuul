<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Finance\PostLedgerTransaction;
use App\Actions\Finance\ReceivePayment;
use App\Actions\Finance\SetBudget;
use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use App\Services\Finance\ChartOfAccounts;
use App\Services\Report\ReportRegistry;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The reports a school office and a school board actually ask for.
 */
class FinanceReportTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_every_finance_report_is_registered_and_named(): void
    {
        $reports = app(ReportRegistry::class)->all();

        foreach ([
            'student-balances', 'student-aging', 'income-by-fee-type', 'expenses',
            'cash-and-bank', 'general-ledger', 'trial-balance', 'income-statement',
            'balance-sheet', 'budget-variance',
        ] as $key) {
            $this->assertArrayHasKey($key, $reports, "The $key report is missing.");
            $this->assertNotSame('', app(ReportRegistry::class)->get($key)->title());
        }
    }

    public function test_the_trial_balance_has_two_sides_that_match(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        app(ChargeStudent::class)->charge($this->enrollment(), 500, 'Term one fees');

        $rows = app(ReportRegistry::class)->get('trial-balance')->rows($this->parameters());
        $total = $rows->last();

        $this->assertSame('Total', $total[1]);
        $this->assertSame($total[3], $total[4]);
        $this->assertSame(500.0, $total[3]);
    }

    public function test_the_income_statement_says_what_is_left_over(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        app(ChargeStudent::class)->charge($this->enrollment(), 900, 'Term one fees');
        $this->spend(400);

        $rows = app(ReportRegistry::class)->get('income-statement')->rows($this->parameters());
        $result = $rows->last();

        $this->assertSame('Surplus', $result[1]);
        $this->assertSame(500.0, $result[2]);
    }

    public function test_the_balance_sheet_meets_on_both_sides(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $enrollment = $this->enrollment();
        app(ChargeStudent::class)->charge($enrollment, 700, 'Term one fees');

        $rows = app(ReportRegistry::class)->get('balance-sheet')->rows($this->parameters());
        $assets = $rows->first(fn (array $row): bool => $row[1] === 'Total assets');
        $check = $rows->last();

        $this->assertSame('Liabilities and equity', $check[1]);
        $this->assertSame($assets[2], $check[2]);
    }

    public function test_the_cash_summary_separates_what_came_in_from_what_went_out(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, 300);
        app(ReceivePayment::class)->receive($enrollment, 20_000);

        $rows = app(ReportRegistry::class)->get('cash-and-bank')->rows($this->parameters());
        $cash = $rows->first(fn (array $row): bool => $row[0] === 'Cash');

        $this->assertSame(0.0, $cash[1]);
        $this->assertSame(200.0, $cash[2]);
        $this->assertSame(0.0, $cash[3]);
        $this->assertSame(200.0, $cash[4]);
    }

    public function test_the_aging_report_puts_an_old_bill_in_an_old_bucket(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, 400, now()->subDays(45));

        $rows = app(ReportRegistry::class)->get('student-aging')->rows($this->parameters());
        $row = $rows->firstWhere(0, $enrollment->admission_number);

        $this->assertNotNull($row, 'The family that owes money is missing from the report.');

        // Columns: admission, name, level, section, then the five buckets.
        $this->assertSame(0.0, $row[4]);
        $this->assertSame(0.0, $row[5]);
        $this->assertSame(400.0, $row[6]);
        $this->assertSame(400.0, $row[9]);
    }

    public function test_the_aging_report_leaves_out_a_family_that_owes_nothing(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, 100, now()->subDays(45));
        app(ReceivePayment::class)->receive($enrollment, 10_000);

        $rows = app(ReportRegistry::class)->get('student-aging')->rows($this->parameters());

        $this->assertNull($rows->firstWhere(0, $enrollment->admission_number));
    }

    public function test_income_by_fee_type_says_what_each_fee_raised(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, 250);
        app(ReceivePayment::class)->receive($enrollment, 10_000);

        $row = app(ReportRegistry::class)->get('income-by-fee-type')->rows($this->parameters())
            ->firstWhere(1, 'Tuition');

        $this->assertNotNull($row, 'The fee is missing from the report.');

        $this->assertSame(1, $row[2]);
        $this->assertSame(250.0, $row[3]);
        $this->assertSame(100.0, $row[6]);
        $this->assertSame(150.0, $row[7]);
    }

    public function test_the_expense_report_keeps_the_fund_beside_the_amount(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        $this->spend(120, 'library');

        $row = app(ReportRegistry::class)->get('expenses')->rows($this->parameters())
            ->firstWhere(4, 'library');

        $this->assertNotNull($row, 'The spending is missing from the report.');

        $this->assertSame('library', $row[4]);
        $this->assertSame(120.0, $row[5]);
    }

    public function test_the_general_ledger_lists_both_sides_of_an_entry(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        app(ChargeStudent::class)->charge($this->enrollment(), 500, 'Term one fees');

        $rows = app(ReportRegistry::class)->get('general-ledger')->rows($this->parameters());

        $this->assertCount(2, $rows);
        $this->assertSame(500.0, $rows->sum(fn (array $row): float => (float) $row[7]));
        $this->assertSame(500.0, $rows->sum(fn (array $row): float => (float) $row[8]));
    }

    public function test_the_budget_variance_report_carries_the_plan_and_the_books(): void
    {
        $this->authorized_user([]);
        $cycle = $this->cycle();
        app(SetBudget::class)->set($cycle, app(ChartOfAccounts::class)->account('tuition_income'), 1_000);
        app(ChargeStudent::class)->charge($this->enrollment(), 250, 'Term one fees');

        $row = app(ReportRegistry::class)->get('budget-variance')
            ->rows($this->parameters() + ['academic_year_id' => $cycle->id])
            ->firstWhere(0, 'Tuition income');

        $this->assertNotNull($row, 'The plan is missing from the report.');

        $this->assertSame(1000.0, $row[3]);
        $this->assertSame(250.0, $row[4]);
        $this->assertSame('No', $row[7]);
    }

    public function test_a_report_only_reads_the_school_that_asked(): void
    {
        $this->authorized_user([]);
        $this->cycle();
        app(ChargeStudent::class)->charge($this->enrollment(), 500, 'Term one fees');

        $elsewhere = School::factory()->create();

        $rows = app(ReportRegistry::class)->get('general-ledger')->rows(['school_id' => $elsewhere->id]);

        $this->assertTrue($rows->isEmpty());
    }

    /**
     * Create a cycle that covers today, so the reports have a window.
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
     * Get the parameters a worker would pass to a report.
     *
     * @return array<string, mixed>
     */
    private function parameters(): array
    {
        return [
            'school_id' => $this->workingSchool()->id,
            'from' => now()->startOfYear()->toDateString(),
            'to' => now()->endOfYear()->toDateString(),
        ];
    }

    /**
     * Create an enrollment whose person belongs to the working school.
     */
    private function enrollment(): StudentRecord
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->memberOf($this->workingSchool(), $enrollment->user);

        return $enrollment->fresh();
    }

    /**
     * Raise a real invoice, so the charge reaches the books as well.
     */
    private function invoiceFor(StudentRecord $enrollment, int $amount, mixed $dueDate = null): FeeInvoice
    {
        $category = FeeCategory::factory()->create(['school_id' => $this->workingSchool()->id]);
        $fee = Fee::factory()->create(['fee_category_id' => $category->id, 'name' => 'Tuition']);

        app(FeeInvoiceService::class)->storeFeeInvoice([
            'issue_date' => ($dueDate ?? now())->toDateString(),
            'due_date' => ($dueDate ?? now())->toDateString(),
            'users' => [$enrollment->user_id],
            'records' => [['fee_id' => $fee->id, 'amount' => $amount, 'waiver' => 0, 'fine' => 0]],
        ]);

        return FeeInvoice::where('user_id', $enrollment->user_id)->latest('id')->firstOrFail();
    }

    /**
     * Spend money out of the bank against an optional fund.
     */
    private function spend(float $amount, ?string $fund = null): void
    {
        $chart = app(ChartOfAccounts::class);

        app(PostLedgerTransaction::class)->post(
            description: 'Something the school bought',
            lines: [
                ['account' => $chart->account('operating_expenses'), 'debit' => $amount, 'fund' => $fund],
                ['account' => $chart->account('bank'), 'credit' => $amount],
            ],
        );
    }
}
