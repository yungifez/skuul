<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
use App\Livewire\CreateFeeInvoiceForm;
use App\Livewire\EditFeeInvoiceForm;
use App\Livewire\ListFeeInvoicesTable;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceBatch;
use App\Models\FinancialPeriod;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Fee\FeeInvoiceService;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Livewire\Livewire;
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
            ->assertSee('id="finance-owed"', false)
            ->assertSee('Overdue invoices')
            ->assertDontSee('More finance pages');

        $content = $response->getContent();
        $summaryPosition = strpos($content, 'id="finance-owed"');
        $tablePosition = strpos($content, 'data-slot="data-table"');

        $this->assertIsInt($summaryPosition);
        $this->assertIsInt($tablePosition);
        $this->assertLessThan($tablePosition, $summaryPosition);
    }

    public function test_the_finance_menu_lists_only_pages_the_user_can_open(): void
    {
        $this->authorized_user(['read fee invoice', 'read fee', 'read expense'])
            ->get('dashboard/fees/fee-invoices')
            ->assertSuccessful()
            ->assertSee('More finance pages')
            ->assertSee(route('fees.index'), false)
            ->assertSee(route('expenses.index'), false)
            ->assertDontSee(route('cash-deposits.index'), false)
            ->assertDontSee(route('budgets.index'), false)
            ->assertDontSee(route('expenses.create'), false);
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
            ->assertSeeLivewire(CreateFeeInvoiceForm::class)
            ->assertSee('wire:submit="save"', false)
            ->assertSee('wire:target="addFees"', false)
            ->assertDontSee('data-slot="native-select"', false);
    }

    public function test_unauthorized_user_cannot_create_fee_invoice(): void
    {
        $this->unauthorized_user();

        Livewire::test(CreateFeeInvoiceForm::class)->assertForbidden();

        $this->get(route('fee-invoices.create'))->assertForbidden();
    }

    public function test_a_bursar_invoices_a_whole_section_in_one_go(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first, $second] = $this->sectionWithTwoStudents();
        [$category, $tuition, $books] = $this->categoryWithTwoFees();

        $this->get(route('fee-invoices.create'))->assertOk()->assertSeeLivewire(CreateFeeInvoiceForm::class);

        Livewire::test(CreateFeeInvoiceForm::class)
            ->set('academicLevelId', (string) $section->academic_level_id)
            ->set('cycleSectionId', (string) $section->id)
            ->call('addStudents')
            ->assertSee($first->user->name)
            ->assertSee($second->user->name)
            ->set('feeCategoryId', (string) $category->id)
            ->call('addFees')
            ->set("lines.{$tuition->id}.amount", 5000)
            ->set("lines.{$tuition->id}.waiver", 500)
            ->set("lines.{$books->id}.amount", 1200)
            ->set('dueDate', now()->addMonth()->toDateString())
            ->assertSee('Create 2 invoices')
            ->assertSee('5,700 each')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('fee-invoices.index'));

        foreach ([$first, $second] as $enrollment) {
            $invoice = FeeInvoice::query()->where('student_record_id', $enrollment->id)->sole();
            $this->assertSame(2, $invoice->feeInvoiceRecords()->count());
        }
    }

    public function test_an_invoice_dated_outside_every_financial_period_is_refused(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first] = $this->sectionWithTwoStudents();
        [$category, $tuition] = $this->categoryWithTwoFees();

        $before = FeeInvoice::query()->count();

        Livewire::test(CreateFeeInvoiceForm::class)
            ->set('issueDate', now()->subYears(2)->toDateString())
            ->set('studentRecordIds', [$first->id])
            ->set('lines', [$tuition->id => ['amount' => 5000, 'waiver' => null, 'fine' => null]])
            ->set('dueDate', now()->toDateString())
            ->call('save')
            ->assertHasErrors('issueDate')
            ->assertNoRedirect();

        $this->assertSame($before, FeeInvoice::query()->count());
    }

    public function test_a_waiver_cannot_be_more_than_the_fee(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first] = $this->sectionWithTwoStudents();
        [$category, $tuition] = $this->categoryWithTwoFees();
        $before = FeeInvoice::query()->count();

        Livewire::test(CreateFeeInvoiceForm::class)
            ->set('studentRecordIds', [$first->id])
            ->set('lines', [$tuition->id => ['amount' => 100, 'waiver' => 150, 'fine' => null]])
            ->set('dueDate', now()->toDateString())
            ->call('save')
            ->assertHasErrors("lines.{$tuition->id}.waiver");

        $this->assertSame($before, FeeInvoice::query()->count());
    }

    public function test_another_schools_fees_and_students_never_reach_an_invoice(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first] = $this->sectionWithTwoStudents();
        $before = FeeInvoice::query()->count();
        $otherSchool = School::factory()->create();
        $foreignCategory = FeeCategory::factory()->create(['school_id' => $otherSchool->id]);
        $foreignFee = Fee::factory()->create(['fee_category_id' => $foreignCategory->id, 'name' => 'Foreign levy']);
        $foreignEnrollment = StudentRecord::factory()->create(['school_id' => $otherSchool->id]);

        $form = Livewire::test(CreateFeeInvoiceForm::class)
            ->set('feeCategoryId', (string) $foreignCategory->id)
            ->call('addFees')
            ->assertSet('lines', [])
            ->assertDontSee('Foreign levy');

        $form->set('lines', [$foreignFee->id => ['amount' => 100, 'waiver' => null, 'fine' => null]])
            ->set('studentRecordIds', [$first->id])
            ->set('dueDate', now()->toDateString())
            ->assertDontSee('Foreign levy')
            ->call('save')
            ->assertHasErrors('issueDate');

        $form->set('lines', [])
            ->set('studentRecordIds', [$foreignEnrollment->id])
            ->call('save')
            ->assertHasErrors('studentRecordIds.0');

        $this->assertSame($before, FeeInvoice::query()->count());
    }

    public function test_a_student_who_left_is_not_added(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first, $second] = $this->sectionWithTwoStudents();
        $second->update(['status' => EnrollmentStatus::Withdrawn]);

        Livewire::test(CreateFeeInvoiceForm::class)
            ->set('academicLevelId', (string) $section->academic_level_id)
            ->set('cycleSectionId', (string) $section->id)
            ->call('addStudents')
            ->assertSet('studentRecordIds', [$first->id]);
    }

    public function test_a_suspended_student_is_still_billed(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first, $second] = $this->sectionWithTwoStudents();
        $second->update(['status' => EnrollmentStatus::Suspended]);

        Livewire::test(CreateFeeInvoiceForm::class)
            ->set('academicLevelId', (string) $section->academic_level_id)
            ->set('cycleSectionId', (string) $section->id)
            ->call('addStudents')
            ->assertSet('studentRecordIds', [$first->id, $second->id]);
    }

    public function test_pressing_create_twice_makes_the_invoices_once(): void
    {
        $this->authorized_user(['create fee invoice']);
        [$section, $first] = $this->sectionWithTwoStudents();
        [$category, $tuition] = $this->categoryWithTwoFees();

        $form = Livewire::test(CreateFeeInvoiceForm::class)
            ->set('studentRecordIds', [$first->id])
            ->set('lines', [$tuition->id => ['amount' => 5000, 'waiver' => null, 'fine' => null]])
            ->set('dueDate', now()->toDateString());

        $form->call('save');
        $form->call('save');

        $this->assertSame(1, FeeInvoice::query()->where('student_record_id', $first->id)->count());
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

    /**
     * Every fee can be taken off an invoice, so the table has to say when
     * nothing is left rather than show a heading over blank space.
     */
    public function test_an_invoice_with_no_fees_says_so()
    {
        $feeInvoice = FeeInvoice::factory()->create();

        $this->authorized_user(['read fee invoice'])
            ->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertSuccessful()
            ->assertSee('No fees are on this invoice yet.');
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

    /**
     * An invoice is financial history. It has to keep naming the person it
     * was raised for, even after that person is removed from the school.
     */
    public function test_an_invoice_still_names_a_removed_student()
    {
        $school = $this->workingSchool();
        $enrollment = StudentRecord::factory()->create(['school_id' => $school->id]);
        $this->memberOf($school, $enrollment->user);
        $name = $enrollment->user->name;

        $feeInvoice = FeeInvoice::factory()->for($enrollment->user)->create([
            'school_id' => $school->id,
            'student_record_id' => $enrollment->id,
        ]);

        $office = $this->authorized_user(['read fee invoice', 'update fee invoice']);
        $enrollment->user->delete();

        $office->get("dashboard/fees/fee-invoices/$feeInvoice->id")
            ->assertSuccessful()
            ->assertSee($name);

        $office->get("dashboard/fees/fee-invoices/$feeInvoice->id/edit")
            ->assertSuccessful()
            ->assertSee($name);

        Livewire::test(ListFeeInvoicesTable::class, ['status' => 'all'])->assertOk();
    }

    /**
     * The summary cards and the table below them show the same money, so they
     * have to name the currency the same way.
     */
    public function test_the_finance_summary_names_its_currency()
    {
        $this->authorized_user(['read fee invoice'])
            ->get('dashboard/fees/fee-invoices')
            ->assertSuccessful()
            ->assertSee(money_text(0))
            ->assertDontSee('>0.00<', false);
    }

    public function test_the_invoice_list_sorts_by_the_date_its_due_date_column_shows(): void
    {
        $this->authorized_user(['read fee invoice']);

        FeeInvoice::factory()->count(2)->create();

        Livewire::test(ListFeeInvoicesTable::class)
            ->call('updateTable', ['sort' => ['key' => 'due_date_label', 'direction' => 'desc']])
            ->assertOk();
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
            ->assertSuccessful()
            ->assertSeeInOrder(['Finance', $feeInvoice->name, 'Edit'])
            ->assertDontSee('Fee Invoices');
    }

    public function test_unauthorized_user_cannot_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create();
        $this->unauthorized_user();

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoice])->assertForbidden();
    }

    public function test_authorized_user_can_update_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $dueDate = $feeInvoice->issue_date->copy()->addDays(10)->format('Y-m-d');
        $this->authorized_user(['update fee invoice']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoice])
            ->set('dueDate', $dueDate)
            ->set('note', 'Second term')
            ->call('saveDetails')
            ->assertHasNoErrors();

        $this->assertSame($dueDate, $feeInvoice->fresh()->due_date->format('Y-m-d'));
        $this->assertSame('Second term', $feeInvoice->fresh()->note);
    }

    public function test_a_due_date_cannot_come_before_the_issue_date()
    {
        $feeInvoice = FeeInvoice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['update fee invoice']);

        Livewire::test(EditFeeInvoiceForm::class, ['feeInvoice' => $feeInvoice])
            ->set('dueDate', $feeInvoice->issue_date->copy()->subDay()->format('Y-m-d'))
            ->call('saveDetails')
            ->assertHasErrors(['dueDate' => 'after_or_equal']);
    }

    public function test_unauthorized_user_cannot_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read fee invoice']);

        Livewire::test(ListFeeInvoicesTable::class, ['status' => 'all'])
            ->call('deleteInvoice', $feeInvoice->id)
            ->assertForbidden();

        $this->assertModelExists($feeInvoice);

        $this->assertNotSoftDeleted($feeInvoice);
    }

    public function test_authorized_user_can_delete_fee_invoice()
    {
        $feeInvoice = FeeInvoice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read fee invoice', 'delete fee invoice']);

        Livewire::test(ListFeeInvoicesTable::class, ['status' => 'all'])
            ->call('deleteInvoice', $feeInvoice->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelExists($feeInvoice);

        $this->assertSoftDeleted($feeInvoice);
    }

    public function test_another_schools_fee_invoice_cannot_be_deleted()
    {
        $theirs = FeeInvoice::factory()->create(['school_id' => School::factory()->create()->id]);
        $this->authorized_user(['read fee invoice', 'delete fee invoice']);

        try {
            Livewire::test(ListFeeInvoicesTable::class, ['status' => 'all'])->call('deleteInvoice', $theirs->id);
            $this->fail('Another school\'s invoice was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($theirs);
        $this->assertNotSoftDeleted($theirs);
    }

    /**
     * Make a section of the working year with two active students.
     *
     * @return array{0: AcademicCycleSection, 1: StudentRecord, 2: StudentRecord}
     */
    private function sectionWithTwoStudents(): array
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        academic_period_context()->setAcademicYear($year);
        $section = AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'academic_level_id' => AcademicLevel::factory()->create(['school_id' => $school->id])->id,
            'status' => AcademicStructureStatus::Active,
        ]);
        $first = StudentRecord::factory()->create(['school_id' => $school->id, 'academic_cycle_section_id' => $section->id]);
        $second = StudentRecord::factory()->create(['school_id' => $school->id, 'academic_cycle_section_id' => $section->id]);

        return [$section, $first->load('user'), $second->load('user')];
    }

    /**
     * @return array{0: FeeCategory, 1: Fee, 2: Fee}
     */
    private function categoryWithTwoFees(): array
    {
        $category = FeeCategory::factory()->create(['school_id' => $this->workingSchool()->id]);

        return [
            $category,
            Fee::factory()->create(['fee_category_id' => $category->id, 'name' => 'Tuition']),
            Fee::factory()->create(['fee_category_id' => $category->id, 'name' => 'Books']),
        ];
    }
}
