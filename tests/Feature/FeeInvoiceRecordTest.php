<?php

namespace Tests\Feature;

use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeeInvoiceRecordTest extends TestCase
{
    use FeatureTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_store_fee_invoice_record()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $fee = Fee::factory()->create();

        $this->unauthorized_user()
            ->post('dashboard/fees/fee-invoices/fee-invoice-records', [
                'fee_invoice_id'   => $feeInvoice->id,
                'fee_id'           => $fee->id,
                'amount'           => 100_000,
                'waiver'           => 80_000,
                'fine'             => 10_000,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoice_records', [
            'fee_invoice_id'  => $feeInvoice->id,
            'fee_id'          => $fee->id,
        ]);
    }

    public function test_authorized_user_can_store_fee_invoice_record()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $fee = Fee::factory()->create();

        $this->authorized_user(['create fee invoice record'])
            ->post('dashboard/fees/fee-invoices/fee-invoice-records', [
                'fee_invoice_id'   => $feeInvoice->id,
                'fee_id'           => $fee->id,
                'amount'           => 100_000,
                'waiver'           => 80_000,
                'fine'             => 10_000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fee_invoice_records', [
            'fee_invoice_id'  => $feeInvoice->id,
            'fee_id'          => $fee->id,
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
        $feeInvoiceRecord = FeeInvoiceRecord::factory()->create();

        $this->authorized_user(['delete fee invoice record'])
            ->delete("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id")
            ->assertRedirect();

        $this->assertModelMissing($feeInvoiceRecord);
    }

    public function test_unauthorized_user_cannot_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = FeeInvoiceRecord::factory()->create();

        $oldPaid = $feeInvoiceRecord->paid;

        $pay = mt_rand(1, $feeInvoiceRecord->amount->minus($feeInvoiceRecord->waiver)->plus($feeInvoiceRecord->fine)->minus($oldPaid)->getAmount()->toInt());

        $this->unauthorized_user()
            ->post("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id/pay", [
                'pay'   => $pay,
            ])
            ->assertForbidden();

        $this->assertNotEquals($pay, $feeInvoiceRecord->fresh()->paid->minus($oldPaid)->getAmount()->toInt());
    }

    public function test_authorized_user_can_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = FeeInvoiceRecord::factory()->create();

        $oldPaid = $feeInvoiceRecord->paid;

        $pay = mt_rand(1, $feeInvoiceRecord->amount->minus($feeInvoiceRecord->waiver)->plus($feeInvoiceRecord->fine)->minus($oldPaid)->getAmount()->toInt());

        $this->authorized_user(['update fee invoice record'])
            ->post("dashboard/fees/fee-invoices/fee-invoice-records/$feeInvoiceRecord->id/pay", [
                'pay'   => $pay,
            ])
            ->assertRedirect();

        $this->assertEquals($pay, $feeInvoiceRecord->fresh()->paid->minus($oldPaid)->getAmount()->toInt());
    }
}
