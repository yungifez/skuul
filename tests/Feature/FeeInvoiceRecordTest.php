<?php

namespace Tests\Feature;

use App\Livewire\EditFeeInvoiceForm;
use App\Livewire\TakeInvoicePayment;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\FinancialPeriod;
use App\Models\PaymentAllocation;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class FeeInvoiceRecordTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public function test_someone_without_the_permission_cannot_add_a_fee(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $fee = $this->feeOfTheWorkingSchool();
        $this->authorized_user(['read fee invoice', 'update fee invoice']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->assertDontSee('Add fee')
            ->set('feeId', $fee->id)
            ->set('newAmount', 100)
            ->call('addLine')
            ->assertForbidden();

        $this->assertDatabaseMissing('fee_invoice_records', ['fee_id' => $fee->id]);
    }

    public function test_the_office_adds_a_fee_to_an_unposted_invoice(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $fee = $this->feeOfTheWorkingSchool();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'create fee invoice record']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->set('isAdding', true)
            ->assertSeeHtml('<label for="fee-category" class="mb-1.5 block text-sm font-medium">Fee category</label>')
            ->assertSeeHtml('<label for="fee" class="mb-1.5 block text-sm font-medium">Fee</label>')
            ->assertSeeHtml('<label for="new-amount" class="mb-1.5 block text-sm font-medium">Amount</label>')
            ->assertSeeHtml('<label for="new-waiver" class="mb-1.5 block text-sm font-medium">Waiver</label>')
            ->assertSeeHtml('<label for="new-fine" class="mb-1.5 block text-sm font-medium">Fine</label>')
            ->set('feeCategoryId', $fee->fee_category_id)
            ->set('feeId', $fee->id)
            ->set('newAmount', 1000)
            ->set('newWaiver', 800)
            ->set('newFine', 100)
            ->call('addLine')
            ->assertHasNoErrors()
            ->assertSet('isAdding', false);

        $this->assertDatabaseHas('fee_invoice_records', [
            'fee_invoice_id' => $feeInvoiceRecord->fee_invoice_id,
            'fee_id' => $fee->id,
            'amount' => 100_000,
        ]);
    }

    public function test_a_waiver_cannot_be_more_than_the_fee(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'update fee invoice record']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->call('startEditingLine', $feeInvoiceRecord->id)
            ->assertSet('lineAmount', 500)
            ->set('lineWaiver', 600)
            ->call('saveLine')
            ->assertHasErrors(['lineWaiver' => 'lte']);

        $this->assertTrue($feeInvoiceRecord->fresh()->waiver->isZero());
    }

    public function test_the_office_changes_a_fee_on_an_unposted_invoice(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'update fee invoice record']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->call('startEditingLine', $feeInvoiceRecord->id)
            ->set('lineAmount', 450)
            ->set('lineFine', 20)
            ->call('saveLine')
            ->assertHasNoErrors()
            ->assertSet('editingLineId', null);

        $this->assertSame(45_000, $feeInvoiceRecord->fresh()->amount->getMinorAmount()->toInt());
        $this->assertSame(2_000, $feeInvoiceRecord->fresh()->fine->getMinorAmount()->toInt());
    }

    /**
     * A posted invoice is in the books. The screen shows its fees but offers
     * nothing the service would refuse.
     */
    public function test_a_posted_invoice_shows_its_fees_locked(): void
    {
        $enrollment = $this->lineOfAnEnrolledStudent()->feeInvoice->studentRecord;
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'create fee invoice record', 'update fee invoice record', 'delete fee invoice record']);
        app(FeeInvoiceService::class)->storeFeeInvoice([
            'issue_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'student_records' => [$enrollment->id],
            'records' => [['fee_id' => $this->feeOfTheWorkingSchool()->id, 'amount' => 100, 'waiver' => 0, 'fine' => 0]],
        ]);
        $posted = FeeInvoice::where('student_record_id', $enrollment->id)->latest('id')->firstOrFail();
        $this->assertNotNull($posted->ledger_transaction_id);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $posted])
            ->assertSee('Posted')
            ->assertDontSee('Add fee')
            ->assertDontSee('startEditingLine', false)
            ->assertDontSee('removeLine', false);
    }

    public function test_someone_without_the_permission_cannot_remove_a_fee(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->call('removeLine', $feeInvoiceRecord->id)
            ->assertForbidden();

        $this->assertModelExists($feeInvoiceRecord);
    }

    public function test_the_office_removes_an_unpaid_fee(): void
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->call('removeLine', $feeInvoiceRecord->id)
            ->assertHasNoErrors();

        $this->assertModelMissing($feeInvoiceRecord);
    }

    /**
     * The allocations that say what a payment settled are removed with the
     * line, so taking the line away would leave the money unexplained.
     */
    public function test_a_fee_with_money_against_it_cannot_be_removed()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        $this->payAgainst($feeInvoiceRecord, '2');

        $this->assertSame(1, PaymentAllocation::query()->where('fee_invoice_record_id', $feeInvoiceRecord->id)->count());

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->call('removeLine', $feeInvoiceRecord->id)
            ->assertHasErrors('lines');

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
            ->assertSee("removeLine({$feeInvoiceRecord->id})", false);

        $this->payAgainst($feeInvoiceRecord, '2');

        $office->get("dashboard/fees/fee-invoices/{$feeInvoiceRecord->fee_invoice_id}/edit")
            ->assertSuccessful()
            ->assertDontSee("removeLine({$feeInvoiceRecord->id})", false)
            ->assertSee('paid');
    }

    /**
     * Paying moved from the line to the invoice, because one payment can
     * cover several fees and the money has to be recorded once.
     */
    public function test_unauthorized_user_cannot_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();

        $this->unauthorized_user();

        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])->assertForbidden();

        $this->assertTrue($feeInvoiceRecord->fresh()->paid->isZero());
    }

    public function test_authorized_user_can_pay_fee_invoice_record()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();

        $this->authorized_user(['read fee invoice', 'update fee invoice']);

        $this->payAgainst($feeInvoiceRecord, '10');

        $this->assertSame(1000, $feeInvoiceRecord->fresh()->paid->getMinorAmount()->toInt());
    }

    /**
     * A line outlives the invoice it sat on, because only the invoice soft
     * deletes. Its policy still has to be able to read the invoice's school.
     */
    public function test_a_line_of_a_deleted_invoice_does_not_break_its_policy()
    {
        $feeInvoiceRecord = $this->lineOfAnEnrolledStudent();
        $this->authorized_user(['read fee invoice', 'update fee invoice', 'delete fee invoice record']);

        $feeInvoiceRecord->feeInvoice->delete();

        $this->assertTrue(auth()->user()->can('delete', $feeInvoiceRecord->fresh()));
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

    private function payAgainst(FeeInvoiceRecord $feeInvoiceRecord, string $amount): void
    {
        Livewire::test(TakeInvoicePayment::class, ['feeInvoice' => $feeInvoiceRecord->feeInvoice])
            ->set('amount', $amount)
            ->set('method', 'cash')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();
    }

    private function feeOfTheWorkingSchool(): Fee
    {
        return Fee::factory()->create([
            'fee_category_id' => FeeCategory::factory()->create(['school_id' => $this->workingSchool()->id])->id,
        ]);
    }
}
