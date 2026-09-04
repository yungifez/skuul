<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceBatch;
use App\Models\FinancialPeriod;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use App\Traits\FeatureTestTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class FeeInvoiceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_unauthorized_user_cannot_view_all_fee_invoices()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-invoices')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_fee_invoices(): void
    {
        $response = $this->authorized_user(['read fee invoice'])
            ->get('dashboard/fees/fee-invoices')
            ->assertSuccessful()
            ->assertSee('data-slot="collapsible" data-state="closed"', false)
            ->assertSee('Finance tasks')
            ->assertSee('Create an invoice');

        $content = $response->getContent();
        $collapsiblePosition = strpos($content, 'data-slot="collapsible"');
        $tablePosition = strpos($content, 'data-slot="data-table"');

        $this->assertIsInt($collapsiblePosition);
        $this->assertIsInt($tablePosition);
        $this->assertLessThan($tablePosition, $collapsiblePosition);
    }

    public function test_authorized_user_can_view_fee_invoices_with_current_enrollment_placement()
    {
        $studentRecord = StudentRecord::factory()->create();
        $school = $this->workingSchool();
        $financialPeriod = FinancialPeriod::query()
            ->where('school_id', $school->id)
            ->where('name', 'Current finance period')
            ->firstOrFail();
        // The list reaches the invoice through its student, so the student
        // needs a membership in the school being worked in.
        $this->memberOf($school, $studentRecord->user);
        // The table lists the running year, newest due date first, ten to a
        // page. The last day of the year puts this invoice on the first page.
        $feeInvoice = FeeInvoice::factory()->for($studentRecord->user)->create([
            'school_id' => $school->id,
            'student_record_id' => $studentRecord->id,
            'financial_period_id' => $financialPeriod->id,
            'due_date' => now()->endOfYear(),
        ]);

        // The screen opens on unpaid invoices, and this one carries no fees.
        $this->authorized_user(['read fee invoice'])
            ->get('dashboard/fees/fee-invoices?status=all')
            ->assertSuccessful()
            ->assertSee($feeInvoice->name);
    }

    public function test_unauthorized_user_cannot_view_create_fee_invoice()
    {
        $this->unauthorized_user()
            ->get('dashboard/fees/fee-invoices/create')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_create_fee_invoice(): void
    {
        $this->authorized_user(['create fee invoice'])
            ->get('dashboard/fees/fee-invoices/create')
            ->assertSuccessful()
            ->assertDontSee('wire:loading.disable', false)
            ->assertSee('wire:target="addFee"', false)
            ->assertSee('style="display: none"', false)
            ->assertSee('class="space-y-6"', false)
            ->assertSee('data-slot="select"', false)
            ->assertDontSee('data-slot="native-select"', false);
    }

    public function test_unauthorized_user_cannot_create_fee_invoice()
    {
        $studentRecords = StudentRecord::factory()->count(10)->create();
        $fees = Fee::factory()->count(4)->create();
        $records = [];
        foreach ($fees as $fee) {
            $amount = mt_rand(100, 10000);
            $waiver = $amount - 10;
            $fine = $amount - 20;
            array_push($records, [
                'fee_id' => $fee->id,
                'amount' => $amount,
                'waiver' => $waiver,
                'fine' => $fine,
            ]);
        }
        $date = now();
        $students = $studentRecords->map(function ($student) {
            return $student->user;
        });

        $this->unauthorized_user()
            ->post('dashboard/fees/fee-invoices', [
                'issue_date' => $date,
                'due_date' => $date->addDay(),
                'note' => $this->faker()->sentence(),
                'users' => $students->pluck('id'),
                'records' => $records,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoices', [
            'user_id' => $students->first()->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);

        $this->assertDatabaseMissing('fee_invoices', [
            'user_id' => $students[2]->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);
    }

    public function test_authorized_user_can_create_fee_invoice()
    {
        $studentRecords = StudentRecord::factory()->count(10)->create();
        $fees = Fee::factory()->count(4)->create();
        $records = [];
        foreach ($fees as $fee) {
            $amount = mt_rand(100, 10000);
            $waiver = $amount - 10;
            $fine = $amount - 20;
            array_push($records, [
                'fee_id' => $fee->id,
                'amount' => $amount,
                'waiver' => $waiver,
                'fine' => $fine,
            ]);
        }
        $date = now();
        $students = $studentRecords->map(function ($student) {
            return $student->user;
        });

        FinancialPeriod::query()->firstOrCreate(
            ['school_id' => $this->workingSchool()->id, 'name' => 'Current finance period'],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );

        $this->authorized_user(['create fee invoice'])
            ->post('dashboard/fees/fee-invoices', [
                'issue_date' => $date->toDateString(),
                'due_date' => Carbon::instance($date)->addDay()->toDateString(),
                'note' => $this->faker()->sentence(),
                'student_records' => $studentRecords->pluck('id')->all(),
                'records' => $records,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoices', [
            'user_id' => $students->first()->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('fee_invoices', [
            'user_id' => $students[2]->id,
            'issue_date' => $date->format('Y-m-d'),
        ]);
    }

    public function test_replaying_an_invoice_batch_does_not_create_duplicate_invoices(): void
    {
        $school = $this->workingSchool();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $category = FeeCategory::factory()->create(['school_id' => $school->id]);
        $fee = Fee::factory()->create(['fee_category_id' => $category->id]);
        $date = now()->toDateString();
        $key = (string) Str::uuid();

        FinancialPeriod::query()->firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Current finance period',
            ],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );

        $payload = [
            'idempotency_key' => $key,
            'issue_date' => $date,
            'due_date' => $date,
            'student_records' => [$enrollment->id],
            'records' => [['fee_id' => $fee->id, 'amount' => 10000, 'waiver' => 0, 'fine' => 0]],
        ];

        $this->authorized_user(['create fee invoice']);
        app(FeeInvoiceService::class)->storeFeeInvoice($payload);
        app(FeeInvoiceService::class)->storeFeeInvoice($payload);

        $this->assertSame(1, FeeInvoice::query()->where('student_record_id', $enrollment->id)->count());
        $this->assertSame(1, FeeInvoiceBatch::query()->where('idempotency_key', $key)->count());
    }

    public function test_unauthorized_user_cannot_view_show_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_show_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['read fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_print_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/print")
            ->assertForbidden();
    }

    public function test_authorized_user_can_print_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['read fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/print")
            ->assertSuccessful()
            ->assertHeader('content-type', 'text/html; charset=UTF-8')
            ->assertSee('data-print-button', false)
            ->assertSee('window.print', false);
    }

    public function test_unauthorized_user_cannot_view_edit_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/edit")
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_page()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['update fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id/edit")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cannot_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $issueDate = $this->faker->date();
        $dueDate = $this->faker->date();

        $this->unauthorized_user()
            ->put("dashboard/fees/fee-invoices/$feeInvoice->id/", [
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoices', [
            'id' => $feeInvoice->id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
        ]);
    }

    public function test_authorized_user_can_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $issueDate = $feeInvoice->issue_date->format('Y-m-d');
        $dueDate = Carbon::parse($issueDate)->addDays(10)->format('Y-m-d');

        $this->authorized_user(['update fee invoice'])
            ->put("dashboard/fees/fee-invoices/$feeInvoice->id/", [
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoices', [
            'id' => $feeInvoice->id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertForbidden();

        $this->assertModelExists($feeInvoice);

        $this->assertNotSoftDeleted($feeInvoice);
    }

    public function test_authorized_user_can_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['delete fee invoice'])
            ->delete("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertRedirect();

        $this->assertModelExists($feeInvoice);

        $this->assertSoftDeleted($feeInvoice);
    }
}
