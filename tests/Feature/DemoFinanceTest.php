<?php

namespace Tests\Feature;

use App\Enums\FinancialPeriodStatus;
use App\Models\Expense;
use App\Models\FeeInvoice;
use App\Models\FinancialPeriod;
use App\Models\StudentPayment;
use App\Services\Finance\StudentLedger;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The finance office of the public demo: invoices in every state, real
 * payments, and a closed and an open financial period.
 */
class DemoFinanceTest extends TestCase
{
    use RefreshDatabase;

    private DemoSchool $demo;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new DemoSchoolSeeder;
        Model::unguarded(fn () => $seeder->run());
        $this->demo = $seeder->demoSchool();
    }

    public function test_last_year_is_closed_and_this_year_is_open(): void
    {
        $periods = FinancialPeriod::query()->where('school_id', $this->demo->campus->id)->orderBy('starts_on')->get();

        $this->assertCount(2, $periods);
        $this->assertSame(FinancialPeriodStatus::Closed, $periods[0]->status);
        $this->assertSame(FinancialPeriodStatus::Open, $periods[1]->status);
        $this->assertTrue($periods[1]->coversDate(school_today($this->demo->campus)));
        $this->assertStringEndsWith('school year', $periods[1]->name);

        $this->actingAs($this->demo->schoolAdmin)
            ->get(route('fee-invoices.index'))
            ->assertOk()
            ->assertSee($periods[0]->name)
            ->assertSee($periods[1]->name);
    }

    public function test_the_invoice_list_mixes_paid_part_paid_unpaid_and_overdue_invoices(): void
    {
        $invoices = FeeInvoice::query()->where('school_id', $this->demo->campus->id)->with('feeInvoiceRecords.fee', 'allocations')->get();
        $today = school_today($this->demo->campus);

        $this->assertCount(45, $invoices);
        $this->assertTrue($invoices->contains(fn (FeeInvoice $invoice): bool => $invoice->balance->isZero()));
        $this->assertTrue($invoices->contains(fn (FeeInvoice $invoice): bool => $invoice->balance->isPositive() && $invoice->allocations->isNotEmpty()));
        $this->assertTrue($invoices->contains(fn (FeeInvoice $invoice): bool => $invoice->allocations->isEmpty() && $invoice->due_date->lt($today)));
        $this->assertTrue($invoices->contains(fn (FeeInvoice $invoice): bool => $invoice->allocations->isEmpty() && $invoice->due_date->gt($today)));

        $feeNames = $invoices->flatMap(fn (FeeInvoice $invoice) => $invoice->feeInvoiceRecords->pluck('fee.name'))->unique()->sort()->values()->all();
        $this->assertSame(['Activity fee', 'Athletics participation fee', 'Lab fee', 'Science museum field trip', 'Yearbook'], $feeNames);

        $this->assertGreaterThan(10, StudentPayment::query()->where('school_id', $this->demo->campus->id)->whereNotNull('ledger_transaction_id')->count());
        $this->assertSame(2, Expense::query()->where('school_id', $this->demo->campus->id)->count());
    }

    public function test_ethan_owes_part_of_his_fees_and_the_office_can_take_the_rest(): void
    {
        $enrollment = $this->demo->demoStudent->studentRecords()->where('school_id', $this->demo->campus->id)->sole();
        $invoice = FeeInvoice::query()->where('student_record_id', $enrollment->id)->oldest('id')->firstOrFail();

        $this->assertSame(145.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(11_000, $invoice->balance->getMinorAmount()->toInt());

        $this->actingAs($this->demo->schoolAdmin)
            ->get(route('fee-invoices.pay', ['fee_invoice' => $invoice]))
            ->assertOk()
            ->assertSee($invoice->name);

        $this->actingAs($this->demo->demoParent)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertOk()
            ->assertSee('145.00');
    }
}
