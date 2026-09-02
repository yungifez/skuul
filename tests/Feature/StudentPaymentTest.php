<?php

namespace Tests\Feature;

use App\Actions\Finance\ApplyStudentCredit;
use App\Actions\Finance\ReceivePayment;
use App\Actions\Finance\RefundStudent;
use App\Actions\Finance\ReversePayment;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FinancialPeriod;
use App\Models\PaymentAllocation;
use App\Models\School;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use App\Services\Finance\PaymentChannelRegistry;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Money a family hands over, and the fees it settles.
 *
 * What an invoice has been paid is never a column somebody writes. It is the
 * sum of the allocations against it, so the screens and the books can only
 * ever say the same thing.
 */
class StudentPaymentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_payment_clears_the_oldest_fee_first(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 300], ['amount' => 200]]);

        app(ReceivePayment::class)->receive($enrollment, 30_000);

        $lines = $invoice->feeInvoiceRecords()->orderBy('id')->get();
        $this->assertSame(30_000, $lines[0]->paid->getMinorAmount()->toInt());
        $this->assertSame(0, $lines[1]->paid->getMinorAmount()->toInt());
        $this->assertTrue($lines[0]->outstanding->isZero());
    }

    public function test_one_payment_covers_several_invoices(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $first = $this->invoiceFor($enrollment, [['amount' => 100]], now()->subMonth());
        $second = $this->invoiceFor($enrollment, [['amount' => 100]], now());

        $payment = app(ReceivePayment::class)->receive($enrollment, 15_000);

        $this->assertSame(2, $payment->allocations()->count());
        $this->assertSame(10_000, $first->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(5_000, $second->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_money_above_what_is_owed_is_held_as_credit(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, [['amount' => 100]]);

        $payment = app(ReceivePayment::class)->receive($enrollment, 15_000);

        $this->assertSame(5_000, $payment->unallocated()->getMinorAmount()->toInt());
        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
        $this->assertSame(50.0, app(StudentLedger::class)->unappliedCredit($enrollment));
        $this->assertSame(0.0, app(StudentLedger::class)->balance($enrollment));
    }

    public function test_credit_the_school_holds_pays_a_later_invoice(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 20_000);
        $invoice = $this->invoiceFor($enrollment, [['amount' => 150]]);

        $applied = app(ApplyStudentCredit::class)->apply($enrollment);

        $this->assertSame(15_000, $applied);
        $this->assertSame(15_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
        $this->assertSame(0.0, app(StudentLedger::class)->balance($enrollment));
    }

    public function test_a_named_fee_cannot_be_given_more_than_it_owes(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        $line = $invoice->feeInvoiceRecords()->sole();

        $this->expectException(InvalidValueException::class);

        app(ReceivePayment::class)->receive($enrollment, 20_000, allocations: [$line->id => 20_000]);
    }

    public function test_a_payment_cannot_settle_another_student_s_fee(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $other = $this->enrollment();
        $line = $this->invoiceFor($other, [['amount' => 100]])->feeInvoiceRecords()->sole();

        $this->expectException(InvalidValueException::class);

        app(ReceivePayment::class)->receive($enrollment, 10_000, allocations: [$line->id => 10_000]);
    }

    public function test_an_invoice_is_due_until_its_allocations_cover_it(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        $this->assertTrue(FeeInvoice::isDue()->whereKey($invoice->id)->exists());

        app(ReceivePayment::class)->receive($enrollment, 10_000);

        $this->assertFalse(FeeInvoice::isDue()->whereKey($invoice->id)->exists());
        $this->assertTrue(FeeInvoice::isPaid()->whereKey($invoice->id)->exists());
        $this->assertTrue($invoice->fresh()->isSettled());
    }

    public function test_a_payment_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $payment = app(ReceivePayment::class)->receive($this->enrollment(), 5_000);

        $this->expectException(RuntimeException::class);

        $payment->update(['reference' => 'Something else']);
    }

    public function test_taking_a_payment_back_puts_the_fee_back_on_the_invoice(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        $payment = app(ReceivePayment::class)->receive($enrollment, 10_000);

        app(ReversePayment::class)->reverse($payment, 'The cheque bounced');

        $this->assertSame(0, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $this->assertTrue($invoice->fresh()->balance->isPositive());
        $this->assertSame(100.0, app(StudentLedger::class)->balance($enrollment));
        $this->assertTrue($payment->fresh()->isReversed());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::PaymentReversed)->first());
    }

    public function test_a_payment_cannot_be_taken_back_twice(): void
    {
        $this->authorized_user([]);
        $payment = app(ReceivePayment::class)->receive($this->enrollment(), 5_000);
        app(ReversePayment::class)->reverse($payment, 'Wrong student');

        $this->expectException(InvalidValueException::class);

        app(ReversePayment::class)->reverse($payment->fresh(), 'Wrong again');
    }

    public function test_taking_a_payment_back_takes_its_credit_away(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $payment = app(ReceivePayment::class)->receive($enrollment, 5_000);

        app(ReversePayment::class)->reverse($payment, 'Paid by the wrong family');

        $this->assertSame(0, app(ApplyStudentCredit::class)->creditHeld($enrollment));
        $this->assertSame(0.0, app(StudentLedger::class)->unappliedCredit($enrollment));
    }

    public function test_only_money_the_school_holds_can_be_given_back(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        $this->expectException(InvalidValueException::class);

        app(RefundStudent::class)->refund($enrollment, 8_000, 'The family asked for it back');
    }

    public function test_a_refund_lowers_the_credit_and_the_books(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        $refund = app(RefundStudent::class)->refund($enrollment, 2_000, 'The family asked for it back');

        $this->assertSame(-2_000, $refund->amount->getMinorAmount()->toInt());
        $this->assertSame(3_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
        $this->assertSame(30.0, app(StudentLedger::class)->unappliedCredit($enrollment));
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::StudentRefunded)->first());
    }

    public function test_a_refund_needs_a_reason(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        $this->expectException(InvalidValueException::class);

        app(RefundStudent::class)->refund($enrollment, 1_000, '  ');
    }

    public function test_the_office_can_take_a_payment_from_the_invoice_screen(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        $actor->get(route('fee-invoices.pay', $invoice->id))
            ->assertOk()
            ->assertSee('How did the money reach the school?');

        $actor->post(route('fee-invoices.pay.store', $invoice->id), [
            'amount' => 60,
            'method' => 'cash',
            'spread' => 'oldest_first',
            'received_on' => now()->toDateString(),
        ])->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(6_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(1, StudentPayment::where('student_record_id', $enrollment->id)->count());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::PaymentReceived)->first());
    }

    public function test_the_office_can_name_the_fee_a_payment_settles(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100], ['amount' => 100]]);
        $lines = $invoice->feeInvoiceRecords()->orderBy('id')->get();

        $actor->post(route('fee-invoices.pay.store', $invoice->id), [
            'amount' => 40,
            'method' => 'bank_transfer',
            'spread' => 'by_line',
            'lines' => [$lines[1]->id => 40],
        ])->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(0, $lines[0]->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(4_000, $lines[1]->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_the_screen_refuses_a_way_to_pay_the_school_does_not_take(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        $actor->post(route('fee-invoices.pay.store', $invoice->id), [
            'amount' => 10,
            'method' => 'carrier-pigeon',
        ])->assertSessionHasErrors('method');

        $this->assertSame(0, $invoice->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_the_student_account_screen_answers_the_parent_at_the_counter(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(ReceivePayment::class)->receive($enrollment, 12_000);

        $actor->get(route('student-accounts.show', $enrollment->id))
            ->assertOk()
            ->assertSee($invoice->name)
            ->assertSee('Held for this student');
    }

    public function test_only_a_named_person_can_give_money_back(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        $actor->post(route('student-accounts.refund', $enrollment->id), [
            'amount' => 10,
            'method' => 'cash',
            'reason' => 'The family asked for it back',
        ])->assertForbidden();

        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_a_school_cannot_read_another_school_s_account(): void
    {
        $this->authorized_user(['read fee invoice']);
        $outsider = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->get(route('student-accounts.show', $outsider->id))->assertForbidden();
    }

    public function test_a_way_to_pay_is_one_class_and_one_line(): void
    {
        $channels = app(PaymentChannelRegistry::class);

        $this->assertTrue($channels->has('cash'));
        $this->assertSame('cash', $channels->get('cash')->accountPurpose());
        $this->assertSame('bank', $channels->get('bank_transfer')->accountPurpose());
        $this->assertFalse($channels->get('cash')->needsReference());

        // A provider with no keys set is never offered.
        $this->assertArrayNotHasKey('stripe', $channels->all());
        $this->assertSame('Card payment (Stripe)', $channels->get('stripe')->label());
    }

    public function test_an_allocation_cannot_be_changed_or_deleted(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        $this->expectException(RuntimeException::class);

        PaymentAllocation::first()->delete();
    }

    /**
     * Create an enrollment whose person belongs to the working school.
     */
    private function enrollment(): StudentRecord
    {
        $school = $this->workingSchool();

        FinancialPeriod::query()->firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Term one'],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );

        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $this->memberOf($school, $enrollment->user);

        return $enrollment->fresh();
    }

    /**
     * Raise a real invoice, so the charge reaches the books as well.
     *
     * @param  array<int, array{amount: int}>  $lines
     */
    private function invoiceFor(StudentRecord $enrollment, array $lines, mixed $dueDate = null): FeeInvoice
    {
        $category = FeeCategory::factory()->create(['school_id' => $this->workingSchool()->id]);

        $records = [];

        foreach ($lines as $index => $line) {
            $fee = Fee::factory()->create([
                'fee_category_id' => $category->id,
                'name' => "Fee $index ".fake()->unique()->word(),
            ]);

            $records[] = [
                'fee_id' => $fee->id,
                'amount' => $line['amount'],
                'waiver' => 0,
                'fine' => 0,
            ];
        }

        app(FeeInvoiceService::class)->storeFeeInvoice([
            'issue_date' => ($dueDate ?? now())->toDateString(),
            'due_date' => ($dueDate ?? now())->toDateString(),
            'student_records' => [$enrollment->id],
            'records' => $records,
        ]);

        return FeeInvoice::where('user_id', $enrollment->user_id)->latest('id')->firstOrFail();
    }
}
