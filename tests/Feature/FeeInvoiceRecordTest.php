<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\FinancialPeriod;
use App\Models\PaymentAllocation;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeeInvoiceRecordTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_unauthorized_user_cannot_store_fee_invoice_record()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $fee = Fee::factory()->create();

        $this->unauthorized_user()
            ->post('dashboard/fees/fee-invoices/fee-invoice-records', [
                'fee_invoice_id' => $feeInvoice->id,
                'fee_id' => $fee->id,
                'amount' => 100_000,
                'waiver' => 80_000,
                'fine' => 10_000,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoice_records', [
            'fee_invoice_id' => $feeInvoice->id,
            'fee_id' => $fee->id,
        ]);
    }

    public function test_authorized_user_can_store_fee_invoice_record()
    {
        $school = $this->workingSchool();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $this->memberOf($school, $enrollment->user);
        $feeInvoice = FeeInvoice::factory()->for($enrollment->user)->create([
            'school_id' => $school->id,
            'student_record_id' => $enrollment->id,
        ]);
        $fee = Fee::factory()->create([
            'fee_category_id' => FeeCategory::factory()->create(['school_id' => $school->id])->id,
        ]);

        $this->authorized_user(['create fee invoice record'])
            ->post('dashboard/fees/fee-invoices/fee-invoice-records', [
                'fee_invoice_id' => $feeInvoice->id,
                'fee_id' => $fee->id,
                'amount' => 100_000,
                'waiver' => 80_000,
                'fine' => 10_000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoice_records', [
            'fee_invoice_id' => $feeInvoice->id,
            'fee_id' => $fee->id,
        ]);
    }

    public function test_unauthorized_user_cannot_delete_fee_invoice_record()
    {
        $feeInvoiceRecord = FeeInvoiceRecord::factory()->create();

        $this->unauthorized_user()
            ->delete("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id")
            ->assertForbidden();

        $this->assertModelExists($feeInvoiceRecord);
    }

    public function test_authorized_user_can_delete_fee_invoice_record()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();

        $this->authorized_user(['delete fee invoice record'])
            ->delete("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id")
            ->assertRedirect();

        $this->assertModelMissing($feeInvoiceRecord);
    }

    /**
     * The allocations that say what a payment settled are removed with the
     * line, so taking the line away would leave the money unexplained.
     */
    public function test_a_fee_with_money_against_it_cannot_be_removed()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $office = $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        $office->post(route('fee-invoices.pay.store', $feeInvoiceRecord->fee_invoice_id), [
            'amount' => 2,
            'method' => 'cash',
            'spread' => 'oldest_first',
        ])->assertRedirect();

        $this->assertSame(1, PaymentAllocation::query()->where('fee_invoice_record_id', $feeInvoiceRecord->id)->count());

        $office->delete("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id")
            ->assertRedirect()
            ->assertSessionHas('danger');

        $this->assertModelExists($feeInvoiceRecord);
        $this->assertSame(1, PaymentAllocation::query()->where('fee_invoice_record_id', $feeInvoiceRecord->id)->count());
    }

    /**
     * The edit screen must not offer a button the service will refuse.
     */
    public function test_the_edit_screen_hides_the_delete_button_on_a_paid_fee()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $office = $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        $office->get("dashboard/fees/fee-invoices/{$feeInvoiceRecord->fee_invoice_id}/edit")
            ->assertSuccessful()
            ->assertSee('Continue With Delete');

        $office->post(route('fee-invoices.pay.store', $feeInvoiceRecord->fee_invoice_id), [
            'amount' => 2,
            'method' => 'cash',
            'spread' => 'oldest_first',
        ])->assertRedirect();

        $office->get("dashboard/fees/fee-invoices/{$feeInvoiceRecord->fee_invoice_id}/edit")
            ->assertSuccessful()
            ->assertDontSee('Continue With Delete')
            ->assertSee('has been paid against this fee');
    }

    /**
     * Paying moved from the line to the invoice, because one payment can
     * cover several fees and the money has to be recorded once.
     */
    public function test_unauthorized_user_cannot_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();

        $this->unauthorized_user()
            ->post(route('fee-invoices.pay.store', $feeInvoiceRecord->fee_invoice_id), [
                'amount' => 10,
                'method' => 'cash',
            ])
            ->assertForbidden();

        $this->assertTrue($feeInvoiceRecord->fresh()->paid->isZero());
    }

    public function test_authorized_user_can_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();

        $this->authorized_user(['read fee invoice', 'update fee invoice'])
            ->post(route('fee-invoices.pay.store', $feeInvoiceRecord->fee_invoice_id), [
                'amount' => 10,
                'method' => 'cash',
                'spread' => 'oldest_first',
            ])
            ->assertRedirect();

        $this->assertSame(1000, $feeInvoiceRecord->fresh()->paid->getMinorAmount()->toInt());
    }

    /**
     * A line outlives the invoice it sat on, because only the invoice soft
     * deletes. Its policy still has to be able to read the invoice's school.
     */
    public function test_a_line_of_a_deleted_invoice_does_not_break_its_policy()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $office = $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        $feeInvoiceRecord->feeInvoice->delete();

        $office->delete("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id")
            ->assertRedirect();
    }

    /**
     * Build an invoice line whose student belongs to the working school.
     */
    private function lineOfAnEnrolledStudent(): FeeInvoiceRecord
    {
        $school = $this->workingSchool();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $this->memberOf($school, $enrollment->user);

        FinancialPeriod::query()->firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Term one'],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );

        $feeInvoice = FeeInvoice::factory()->for($enrollment->user)->create([
            'school_id' => $school->id,
            'student_record_id' => $enrollment->id,
            'financial_period_id' => FinancialPeriod::query()
                ->where('school_id', $school->id)
                ->where('name', 'Term one')
                ->value('id'),
        ]);

        $fee = Fee::factory()->create([
            'fee_category_id' => FeeCategory::factory()->create(['school_id' => $school->id])->id,
        ]);

        return FeeInvoiceRecord::factory()->create([
            'fee_invoice_id' => $feeInvoice->id,
            'fee_id' => $fee->id,
            'amount' => 500,
            'waiver' => 0,
            'fine' => 0,
        ]);
    }
}
