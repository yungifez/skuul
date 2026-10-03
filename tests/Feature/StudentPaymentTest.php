<?php

namespace Tests\Feature;

use App\Actions\Finance\ApplyStudentCredit;
use App\Actions\Finance\ReceivePayment;
use App\Actions\Finance\RecordStudentPayment;
use App\Actions\Finance\RefundStudent;
use App\Actions\Finance\RelieveStudentFees;
use App\Actions\Finance\ReversePayment;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Livewire\ShowStudentAccount;
use App\Livewire\TakeInvoicePayment;
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
use App\Services\Finance\ChartOfAccounts;
use App\Services\Finance\PaymentChannelRegistry;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
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

    /**
     * An invoice that still owes money asks for it. Printing it gives an
     * invoice with the amount due, not a receipt.
     */
    public function test_an_unpaid_invoice_offers_to_take_payment_and_prints_as_an_invoice(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]], now()->subWeek());

        $this->get(route('fee-invoices.show', $invoice))
            ->assertSuccessful()
            ->assertSee('Overdue')
            ->assertSee('Take payment')
            ->assertDontSee('Print receipt');

        $this->get(route('fee-invoices.print', $invoice))
            ->assertSuccessful()
            ->assertSee('Amount due')
            ->assertDontSee('Paid in full');
    }

    /**
     * Once the allocations cover every fee, the invoice has done its job.
     * The page offers the receipt, and the print becomes one.
     */
    public function test_a_paid_invoice_offers_and_prints_a_receipt(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        $payment = app(ReceivePayment::class)->receive($enrollment, 10_000, reference: 'BANK-777');

        $this->get(route('fee-invoices.show', $invoice))
            ->assertSuccessful()
            ->assertDontSee('Not paid')
            ->assertSee('Print receipt')
            ->assertDontSee('Take payment')
            ->assertSee(route('student-payments.receipt', $payment), false);

        $this->get(route('fee-invoices.print', $invoice))
            ->assertSuccessful()
            ->assertSee('Receipt')
            ->assertSee('Paid in full')
            ->assertSee('BANK-777')
            ->assertDontSee('Amount due');
    }

    /**
     * An invoice due today is not late yet.
     */
    public function test_an_invoice_due_today_is_not_overdue(): void
    {
        $this->authorized_user(['read fee invoice']);
        $invoice = $this->invoiceFor($this->enrollment(), [['amount' => 100]], now());

        $this->get(route('fee-invoices.show', $invoice))
            ->assertSuccessful()
            ->assertSee('Not paid')
            ->assertDontSee('Overdue');
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

    public function test_one_bank_reference_is_recorded_once_for_a_learner(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        $sibling = $this->enrollment();
        $first = app(ReceivePayment::class)->receive($enrollment, 5_000, reference: 'TRF-1');

        try {
            app(ReceivePayment::class)->receive($enrollment, 5_000, reference: ' trf-1 ');
            $this->fail('The same transfer was recorded twice.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('TRF-1', $exception->getMessage());
        }

        app(ReceivePayment::class)->receive($sibling, 5_000, reference: 'TRF-1');
        app(ReversePayment::class)->reverse($first, 'Keyed against the wrong fee');
        app(ReceivePayment::class)->receive($enrollment, 5_000, reference: 'TRF-1');

        $this->assertSame(4, StudentPayment::query()->count());
    }

    public function test_a_refund_does_not_hold_on_to_its_bank_reference(): void
    {
        $this->authorized_user([]);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);
        app(RefundStudent::class)->refund($enrollment, 5_000, 'Overpaid', reference: 'TRF-9');

        $payment = app(ReceivePayment::class)->receive($enrollment, 5_000, reference: 'TRF-9');

        $this->assertSame('TRF-9', $payment->reference);
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

    public function test_pressing_take_payment_twice_records_one_payment(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '40')
            ->set('method', 'cash')
            ->call('save')
            ->call('save')
            ->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(1, StudentPayment::where('student_record_id', $enrollment->id)->count());
        $this->assertSame(4_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_the_payment_form_names_every_field(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $invoice = $this->invoiceFor($this->enrollment(), [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->assertSeeHtml('<label for="payment-amount" class="mb-1.5 block text-sm font-medium">Amount</label>')
            ->assertSeeHtml('<label for="payment-received-on" class="mb-1.5 block text-sm font-medium">Received on</label>')
            ->assertSeeHtml('<legend class="mb-1.5 text-sm font-medium">Paid by</legend>')
            ->assertSeeHtml('<label for="payment-reference" class="mb-1.5 block text-sm font-medium">Reference</label>')
            ->assertSeeHtml('<label for="payment-note" class="mb-1.5 block text-sm font-medium">Note</label>');
    }

    public function test_a_refused_payment_can_be_corrected_and_taken(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(ReceivePayment::class)->receive($enrollment, 1_000, reference: 'BANK-1');

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '40')
            ->set('method', 'cash')
            ->set('reference', 'bank-1')
            ->call('save')
            ->assertHasErrors('amount')
            ->set('reference', 'BANK-2')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(2, StudentPayment::where('student_record_id', $enrollment->id)->count());
    }

    public function test_the_office_can_take_a_payment_from_the_invoice_screen(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        $actor->get(route('fee-invoices.pay', $invoice->id))
            ->assertOk()
            ->assertSeeLivewire(TakeInvoicePayment::class);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '60')
            ->set('method', 'cash')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(6_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(1, StudentPayment::where('student_record_id', $enrollment->id)->count());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::PaymentReceived)->first());
    }

    public function test_a_campus_still_collects_its_invoice_after_the_learner_moves_on(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        $newCampus = School::factory()->create();
        $enrollment->forceFill(['school_id' => $newCampus->id])->save();

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '60')
            ->set('method', 'cash')
            ->call('save')
            ->assertHasNoErrors();

        $payment = StudentPayment::where('student_record_id', $enrollment->id)->sole();
        $this->assertSame($this->workingSchool()->id, $payment->school_id);
        $this->assertSame(6_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $owed = app(StudentLedger::class)->balancesByCampus($enrollment->fresh())->sole();
        $this->assertSame($this->workingSchool()->id, $owed['school']->id);
        $this->assertSame(40.0, $owed['balance']);
        $this->assertSame(0.0, app(StudentLedger::class)->unappliedCredit($enrollment->fresh()));
    }

    public function test_a_payment_to_the_old_campus_settles_what_is_owed_there(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $this->invoiceFor($enrollment, [['amount' => 100]]);
        $newCampus = School::factory()->create();
        $enrollment->forceFill(['school_id' => $newCampus->id])->save();

        app(RecordStudentPayment::class)->record($enrollment->fresh(), 60, schoolId: $this->workingSchool()->id);

        $ledger = app(StudentLedger::class);
        $this->assertSame(40.0, $ledger->balance($enrollment->fresh(), $this->workingSchool()->id));
        $this->assertSame(0.0, $ledger->unappliedCredit($enrollment->fresh(), $this->workingSchool()->id));
    }

    public function test_the_office_can_name_the_fee_a_payment_settles(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100], ['amount' => 100]]);
        $lines = $invoice->feeInvoiceRecords()->orderBy('id')->get();

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '40')
            ->set('method', 'bank_transfer')
            ->set('reference', 'TRF-2210')
            ->set('splitByFee', true)
            ->set('lines', [$lines[1]->id => '40'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-invoices.show', $invoice->id));

        $this->assertSame(0, $lines[0]->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(4_000, $lines[1]->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_large_sums_owed_get_a_full_row_on_a_phone(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 12_345_678]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->assertSeeHtmlInOrder(['<div class="col-span-2 sm:col-span-1">', 'Owed', 'id="payment-owed"']);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertSeeHtml('grid grid-cols-1 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-2');
    }

    public function test_a_cheque_needs_its_number_and_cash_does_not(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '10')
            ->set('method', 'cheque')
            ->call('save')
            ->assertHasErrors(['reference' => 'required'])
            ->set('reference', 'CHQ 004512')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '10')
            ->set('method', 'cash')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_money_given_back_by_cheque_needs_its_number(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->set('isRefunding', true)
            ->set('refundAmount', '10')
            ->set('refundReason', 'The family asked for it back')
            ->set('refundMethod', 'cheque')
            ->call('refund')
            ->assertHasErrors(['refundReference' => 'required']);

        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_the_screen_refuses_a_way_to_pay_the_school_does_not_take(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '10')
            ->set('method', 'carrier-pigeon')
            ->call('save')
            ->assertHasErrors('method');

        $this->assertSame(0, $invoice->fresh()->paid->getMinorAmount()->toInt());
    }

    /**
     * A third decimal place is not money. It must be refused, not rounded.
     */
    public function test_the_payment_screen_refuses_fractions_of_the_smallest_coin(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $invoice = $this->invoiceFor($this->enrollment(), [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '10.005')
            ->call('save')
            ->assertHasErrors(['amount' => 'decimal']);

        $this->assertTrue($invoice->fresh()->paid->isZero());
    }

    /**
     * The office names more than a fee still owes. The action refuses it,
     * and the screen says so next to the fees.
     */
    public function test_the_payment_screen_reports_a_split_the_fees_cannot_take(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $invoice = $this->invoiceFor($this->enrollment(), [['amount' => 100], ['amount' => 100]]);
        $lines = $invoice->feeInvoiceRecords()->orderBy('id')->get();

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])
            ->set('amount', '150')
            ->set('splitByFee', true)
            ->set('lines', [$lines[0]->id => '150'])
            ->call('save')
            ->assertHasErrors('lines')
            ->assertNoRedirect();

        $this->assertTrue($invoice->fresh()->paid->isZero());
    }

    public function test_someone_who_cannot_update_invoices_cannot_open_the_payment_screen(): void
    {
        $this->authorized_user(['read fee invoice']);
        $invoice = $this->invoiceFor($this->enrollment(), [['amount' => 100]]);

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $invoice])->assertForbidden();
        $this->get(route('fee-invoices.pay', $invoice))->assertForbidden();
    }

    public function test_the_student_account_screen_answers_the_parent_at_the_counter(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(ReceivePayment::class)->receive($enrollment, 12_000);

        $actor->get(route('student-accounts.show', $enrollment->id))
            ->assertOk()
            ->assertSeeLivewire(ShowStudentAccount::class);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertSee($invoice->name)
            ->assertSee('Credit held')
            ->assertSee('Receipt')
            ->assertDontSee('Give money back');
    }

    public function test_only_a_named_person_can_give_money_back(): void
    {
        $actor = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->set('isRefunding', true)
            ->set('refundAmount', '10')
            ->set('refundReason', 'The family asked for it back')
            ->call('refund')
            ->assertForbidden();

        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_the_account_screen_gives_held_money_back(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 5_000);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->call('$set', 'isRefunding', true)
            ->assertSeeHtml('<label for="refund-amount" class="mb-1.5 block text-sm font-medium">Amount</label>')
            ->assertSeeHtml('<label for="refund-method" class="mb-1.5 block text-sm font-medium">Paid out by</label>')
            ->assertSeeHtml('<label for="refund-reference" class="mb-1.5 block text-sm font-medium">Reference</label>')
            ->assertSeeHtml('<label for="refund-reason" class="mb-1.5 block text-sm font-medium">Reason</label>')
            ->set('refundAmount', '80')
            ->set('refundReason', 'The family asked for it back')
            ->call('refund')
            ->assertHasErrors('refundAmount')
            ->set('refundAmount', '20.505')
            ->call('refund')
            ->assertHasErrors(['refundAmount' => 'decimal'])
            ->set('refundAmount', '20')
            ->call('refund')
            ->assertHasNoErrors()
            ->assertSet('isRefunding', false);

        $this->assertSame(3_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_the_account_screen_takes_a_payment_back_with_a_reason(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        $payment = app(ReceivePayment::class)->receive($enrollment, 4_000);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->call('startReversing', $payment->id)
            ->assertSet('reversingPaymentId', $payment->id)
            ->assertSeeHtml('<label for="reverse-reason" class="mb-1.5 block text-sm font-medium">Reason</label>')
            ->call('reversePayment')
            ->assertHasErrors(['reverseReason' => 'required'])
            ->set('reverseReason', 'Recorded against the wrong child')
            ->call('reversePayment')
            ->assertHasNoErrors()
            ->assertSee('Taken back');

        $this->assertTrue($payment->fresh()->isReversed());
        $this->assertSame(0, $invoice->fresh()->paid->getMinorAmount()->toInt());
    }

    public function test_the_fee_relief_form_names_every_field(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->call('startRelieving', $invoice->id)
            ->assertSeeHtml('<label for="relief-line" class="mb-1.5 block text-sm font-medium">Fee</label>')
            ->assertSeeHtml('<label for="relief-amount" class="mb-1.5 block text-sm font-medium">Amount</label>')
            ->assertSeeHtml('<label for="relief-kind" class="mb-1.5 block text-sm font-medium">Kind</label>')
            ->assertSeeHtml('<label for="relief-reason" class="mb-1.5 block text-sm font-medium">Reason</label>');
    }

    public function test_a_refund_sent_again_pays_out_once(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 10_000);

        $screen = Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment]);
        $firstKey = $screen->get('recordKey');

        $screen->set('refundAmount', '20')
            ->set('refundReason', 'The family asked for it back')
            ->call('refund')
            ->assertHasNoErrors();

        $this->assertNotSame($firstKey, $screen->get('recordKey'));

        // A resent request carries the old key and the old form.
        Cache::add('recorded:student-refund:'.current_school_id().':'.$screen->get('recordKey'), true);
        $screen->set('refundAmount', '20')
            ->set('refundReason', 'The family asked for it back')
            ->call('refund');

        $this->assertSame(1, StudentPayment::where('student_record_id', $enrollment->id)->where('amount', '<', 0)->count());
        $this->assertSame(8_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_a_second_refund_on_the_same_screen_is_its_own_refund(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 10_000);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->set('refundAmount', '20')
            ->set('refundReason', 'The family asked for it back')
            ->call('refund')
            ->set('refundAmount', '30')
            ->set('refundReason', 'Uniform paid twice by mistake')
            ->call('refund')
            ->assertHasNoErrors();

        $this->assertSame(5_000, app(ApplyStudentCredit::class)->creditHeld($enrollment));
    }

    public function test_a_campus_cannot_take_back_a_payment_another_campus_took(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $payment = app(ReceivePayment::class)->receive($enrollment, 4_000);
        StudentPayment::query()->whereKey($payment->id)->toBase()->update(['school_id' => School::factory()->create()->id]);

        $screen = Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertDontSeeHtml("startReversing({$payment->id})");

        try {
            $screen->call('startReversing', $payment->id);
            $this->fail('A campus opened the reversal of another campus\'s payment.');
        } catch (ModelNotFoundException) {
        }

        $this->assertFalse($payment->fresh()->isReversed());
    }

    public function test_a_campus_gives_back_what_it_holds_after_the_learner_moves_on(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 20_000);
        $enrollment->forceFill(['school_id' => School::factory()->create()->id])->save();

        $this->get(route('student-accounts.show', $enrollment->id))->assertOk();

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertSee(money_text(200.0))
            ->set('isRefunding', true)
            ->set('refundAmount', '50')
            ->set('refundReason', 'Family moved to the other campus')
            ->call('refund')
            ->assertHasNoErrors();

        $refund = StudentPayment::query()->where('amount', '<', 0)->sole();
        $this->assertSame($this->workingSchool()->id, $refund->school_id);
        $this->assertSame(15_000, app(ApplyStudentCredit::class)->creditHeld($enrollment, $this->workingSchool()->id));
    }

    public function test_a_school_that_never_billed_the_learner_sees_no_account(): void
    {
        $this->authorized_user(['read fee invoice']);
        $elsewhere = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->get(route('student-accounts.show', $elsewhere->id))->assertForbidden();
    }

    public function test_the_account_screen_uses_held_credit_against_what_is_owed(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        app(ReceivePayment::class)->receive($enrollment, 3_000);
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertSee('Use credit against fees')
            ->call('applyCredit')
            ->assertHasNoErrors();

        $this->assertSame(3_000, $invoice->fresh()->paid->getMinorAmount()->toInt());
        $this->assertSame(0, app(ApplyStudentCredit::class)->creditHeld($enrollment));
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

    public function test_the_account_screen_waives_part_of_a_billed_fee(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(ReceivePayment::class)->receive($enrollment, 3_000);
        $line = $invoice->feeInvoiceRecords()->sole();

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertSeeHtml("startRelieving({$invoice->id})")
            ->call('startRelieving', $invoice->id)
            ->assertSet('reliefLineId', (string) $line->id)
            ->assertSeeHtmlInOrder(['</table>', 'id="relief-line"'])
            ->set('reliefAmount', '70.01')
            ->set('reliefReason', 'Staff child discount')
            ->call('relieve')
            ->assertHasErrors('reliefAmount')
            ->set('reliefAmount', '50')
            ->set('reliefReason', 'x')
            ->call('relieve')
            ->assertHasErrors(['reliefReason' => 'min'])
            ->set('reliefReason', 'Staff child discount')
            ->call('relieve')
            ->assertHasNoErrors()
            ->assertSet('relievingInvoiceId', null);

        $this->assertSame(5_000, $line->fresh()->waiver->getMinorAmount()->toInt());
        $this->assertSame(2_000, $invoice->fresh()->balance->getMinorAmount()->toInt());
        $this->assertSame(20.0, app(StudentLedger::class)->balance($enrollment->fresh()));
        $this->assertSame(50.0, app(ChartOfAccounts::class)->account('scholarships')->balance());
        $event = AuditEvent::ofAction(AuditAction::FeesRelieved)->forSubject($invoice)->sole();
        $this->assertSame(5_000, $event->context['amount']);
    }

    public function test_a_write_off_goes_to_bad_debt_and_settles_the_invoice(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        app(RelieveStudentFees::class)->relieveLine($invoice->feeInvoiceRecords()->sole(), 10_000, writeOff: true, reason: 'The family cannot be reached');

        $chart = app(ChartOfAccounts::class);
        $this->assertTrue($invoice->fresh()->isSettled());
        $this->assertSame(0.0, app(StudentLedger::class)->balance($enrollment->fresh()));
        $this->assertSame(100.0, $chart->account('bad_debt')->balance());
        $this->assertSame(0.0, $chart->account('scholarships')->balance());

        $this->expectException(InvalidValueException::class);
        app(RelieveStudentFees::class)->relieveLine($invoice->feeInvoiceRecords()->sole(), 1, writeOff: true, reason: 'Again');
    }

    public function test_a_relief_sent_again_takes_the_fee_off_once(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        $screen = Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->call('startRelieving', $invoice->id)
            ->set('reliefAmount', '10')
            ->set('reliefReason', 'Sibling discount');
        $key = $screen->get('recordKey');
        $screen->call('relieve')->assertHasNoErrors();

        $screen->set('relievingInvoiceId', $invoice->id)
            ->set('reliefLineId', (string) $invoice->feeInvoiceRecords()->sole()->id)
            ->set('reliefAmount', '10')
            ->set('reliefReason', 'Sibling discount');
        Cache::put("recorded:fee-relief:{$this->workingSchool()->id}:{$screen->get('recordKey')}", true);
        $screen->call('relieve');

        $this->assertNotSame($key, $screen->get('recordKey'));
        $this->assertSame(1_000, $invoice->feeInvoiceRecords()->sole()->waiver->getMinorAmount()->toInt());
    }

    public function test_only_a_named_person_can_waive_a_fee(): void
    {
        $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);

        Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertDontSeeHtml("startRelieving({$invoice->id})")
            ->call('startRelieving', $invoice->id)
            ->assertForbidden();

        $this->assertTrue($invoice->feeInvoiceRecords()->sole()->waiver->isZero());
    }

    public function test_a_campus_cannot_waive_an_invoice_another_campus_raised(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        FeeInvoice::query()->whereKey($invoice->id)->toBase()->update(['school_id' => School::factory()->create()->id]);
        $this->invoiceFor($enrollment, [['amount' => 50]]);

        $screen = Livewire::test(ShowStudentAccount::class, ['enrollment' => $enrollment])
            ->assertDontSeeHtml("startRelieving({$invoice->id})");

        $this->expectException(ModelNotFoundException::class);
        $screen->call('startRelieving', $invoice->id);
    }

    public function test_a_fee_cannot_be_waived_below_what_the_campus_books_still_hold(): void
    {
        $this->authorized_user(['read fee invoice', 'refund student payment']);
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment, [['amount' => 100]]);
        app(RelieveStudentFees::class)->waive($enrollment, 80, 'Waived off the invoice by hand');

        $this->expectExceptionMessage('That is more than is still owed on this fee.');
        app(RelieveStudentFees::class)->relieveLine($invoice->feeInvoiceRecords()->sole(), 3_000, writeOff: false, reason: 'Second waiver');
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
