<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\FinancialPeriod;
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
