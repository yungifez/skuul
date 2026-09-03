<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\Fee;
use App\Models\FeeInvoice;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalInvoiceScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_guardian_sees_the_child_invoice_in_the_portal(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        $invoice = $this->invoiceFor($enrollment);

        $this->actingAs($guardian)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee(route('portal.invoices.index', $enrollment), false)
            ->assertSee('Invoices and payments');

        $this->actingAs($guardian)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertOk()
            ->assertSee($invoice->name)
            ->assertSee('Read-only account statement');
    }

    public function test_a_learner_sees_their_own_invoice_in_the_portal(): void
    {
        $enrollment = $this->enrollment();
        $invoice = $this->invoiceFor($enrollment);

        $this->actingAs($enrollment->user)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertOk()
            ->assertSee($invoice->name);
    }

    public function test_a_guardian_with_no_invoices_sees_an_empty_state(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);

        $this->actingAs($guardian)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertOk()
            ->assertSee('No invoices yet');
    }

    public function test_closing_the_invoice_area_removes_its_screen(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);
        features()->enable(Feature::Portal, config: [PortalArea::Invoices->value => false]);

        $this->actingAs($guardian)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertNotFound();
    }

    public function test_a_portal_person_cannot_open_staff_finance_or_calendar_screens(): void
    {
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);

        $this->actingAs($guardian)
            ->get(route('fee-invoices.index'))
            ->assertForbidden();

        $this->actingAs($guardian)
            ->get(route('calendar-events.index'))
            ->assertForbidden();

        $this->actingAs($guardian)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertDontSee(route('fee-invoices.index'), false)
            ->assertDontSee(route('calendar-events.index'), false);
    }

    public function test_a_stranger_cannot_view_a_child_invoice_portal(): void
    {
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $this->actingAs($stranger)
            ->get(route('portal.invoices.index', $enrollment))
            ->assertForbidden();
    }

    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    private function invoiceFor(StudentRecord $enrollment): FeeInvoice
    {
        $invoice = FeeInvoice::factory()->create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
        ]);

        $invoice->feeInvoiceRecords()->create([
            'fee_id' => Fee::factory()->create()->id,
            'amount' => 10000,
            'waiver' => 0,
            'fine' => 0,
        ]);

        return $invoice;
    }
}
