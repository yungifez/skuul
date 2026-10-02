<?php

namespace Tests\Feature;

use App\Actions\Finance\ReceivePayment;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\PaymentAllocation;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Brick\Money\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Invoices from before the ledger reach the books before money settles them.
 */
class LegacyInvoiceBooksTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_payment_on_an_older_invoice_puts_it_in_the_books_first(): void
    {
        $enrollment = $this->enrollment();
        $invoice = $this->olderInvoice($enrollment, 10_000);

        app(ReceivePayment::class)->receive($enrollment, 2_500, onlyInvoice: $invoice->id);

        $this->assertNotNull($invoice->fresh()->ledger_transaction_id);
        $this->assertSame(75.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(7_500, $invoice->fresh()->balance->getMinorAmount()->toInt());
    }

    public function test_money_taken_before_the_books_is_not_charged_again(): void
    {
        $enrollment = $this->enrollment();
        $invoice = $this->olderInvoice($enrollment, 10_000);
        $this->olderPayment($invoice, 4_000);

        app(ReceivePayment::class)->receive($enrollment, 1_000, onlyInvoice: $invoice->id);

        $this->assertSame(50.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertSame(5_000, $invoice->fresh()->balance->getMinorAmount()->toInt());
    }

    public function test_the_command_puts_every_older_invoice_in_the_books_once(): void
    {
        $enrollment = $this->enrollment();
        $this->olderInvoice($enrollment, 10_000);
        $paid = $this->olderInvoice($enrollment, 3_000);
        $this->olderPayment($paid, 3_000);
        $orphan = $this->olderInvoice($enrollment, 2_000);
        $orphan->forceFill(['student_record_id' => null])->save();

        $this->artisan('skuul:bring-invoices-into-books')->assertSuccessful();
        $this->artisan('skuul:bring-invoices-into-books')->expectsOutputToContain('0 invoices')->assertSuccessful();

        $this->assertSame(100.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertNull($orphan->fresh()->ledger_transaction_id);
    }

    public function test_the_account_matches_its_bills_after_the_command(): void
    {
        $enrollment = $this->enrollment();
        $first = $this->olderInvoice($enrollment, 10_000);
        $second = $this->olderInvoice($enrollment, 6_000);
        $this->olderPayment($second, 1_000);

        $this->artisan('skuul:bring-invoices-into-books')->assertSuccessful();

        $owedOnBills = $first->fresh()->balance->plus($second->fresh()->balance)->getMinorAmount()->toInt();
        $this->assertSame($owedOnBills, (int) round(app(StudentLedger::class)->balance($enrollment) * 100));
    }

    private function enrollment(): StudentRecord
    {
        $this->actingAsMemberOf($this->workingSchool());

        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Make an invoice the way an older install left it: no ledger entry.
     *
     * @param  int  $amount  in minor units
     */
    private function olderInvoice(StudentRecord $enrollment, int $amount): FeeInvoice
    {
        $invoice = FeeInvoice::factory()->create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'ledger_transaction_id' => null,
        ]);

        $invoice->feeInvoiceRecords()->create([
            'fee_id' => Fee::factory()->create()->id,
            'amount' => $amount / 100,
            'waiver' => 0,
            'fine' => 0,
        ]);

        return $invoice;
    }

    /**
     * Record money an older install took, which never reached the books.
     */
    private function olderPayment(FeeInvoice $invoice, int $amount): void
    {
        $payment = StudentPayment::create([
            'school_id' => $invoice->school_id,
            'student_record_id' => $invoice->student_record_id,
            'amount' => Money::ofMinor($amount, config('app.currency')),
            'method' => 'cash',
            'received_on' => now()->subYear(),
        ]);

        PaymentAllocation::create([
            'student_payment_id' => $payment->id,
            'fee_invoice_id' => $invoice->id,
            'fee_invoice_record_id' => $invoice->feeInvoiceRecords()->value('id'),
            'amount' => Money::ofMinor($amount, config('app.currency')),
        ]);
    }
}
