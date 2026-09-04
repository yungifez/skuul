<?php

namespace Tests\Feature;

use App\Actions\Library\IssueLoan;
use App\Actions\Library\IssueTitleToSection;
use App\Actions\Library\RenewLoan;
use App\Actions\Library\ReserveTitle;
use App\Actions\Library\ReturnLoan;
use App\Enums\AuditAction;
use App\Enums\Feature;
use App\Enums\LibraryCopyStatus;
use App\Enums\LibraryReservationStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AuditEvent;
use App\Models\FinancialPeriod;
use App\Models\LedgerTransaction;
use App\Models\LibraryCopy;
use App\Models\LibraryLendingRules;
use App\Models\LibraryLoan;
use App\Models\LibraryReservation;
use App\Models\LibraryTitle;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Feature\FeatureManager;
use App\Services\Finance\StudentLedger;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What the school lends, who has it, and when it is due back.
 */
class LibraryTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_library_is_off_until_a_school_turns_it_on(): void
    {
        $this->assertFalse(app(FeatureManager::class)->enabled(Feature::Library));
    }

    public function test_a_campus_lends_on_sensible_rules_it_never_set(): void
    {
        $this->authorized_user([]);

        $rules = LibraryLendingRules::forSchool();

        $this->assertFalse($rules->exists);
        $this->assertSame(14, $rules->loan_days);
        $this->assertSame(3, $rules->learner_limit);
        $this->assertSame(3, $rules->hold_days);
        $this->assertFalse($rules->chargesFines());
    }

    public function test_a_copy_goes_out_and_gets_a_due_date(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());

        $loan = app(IssueLoan::class)->issue($copy, $borrower);

        $this->assertSame($borrower->id, $loan->user_id);
        $this->assertSame(now()->addDays(14)->toDateString(), $loan->due_on->toDateString());
        $this->assertTrue($copy->fresh()->isOut());
        $this->assertFalse($copy->fresh()->canBeLent());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LibraryLoanIssued)->first());
    }

    public function test_two_people_cannot_have_the_same_copy(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));

        $this->expectException(InvalidValueException::class);

        app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));
    }

    public function test_a_copy_that_is_not_on_the_shelf_is_refused(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $copy->status = LibraryCopyStatus::Lost;
        $copy->save();

        $this->expectException(InvalidValueException::class);

        app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));
    }

    public function test_a_copy_from_another_campus_is_refused(): void
    {
        $this->authorized_user([]);
        $elsewhere = School::factory()->create();
        $copy = LibraryCopy::factory()->create(['school_id' => $elsewhere->id]);

        $this->expectException(InvalidValueException::class);

        app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));
    }

    public function test_a_learner_cannot_hold_more_than_the_campus_allows(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'learner_limit' => 1]);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $borrower = $this->memberOf($this->workingSchool(), $enrollment->user);
        app(IssueLoan::class)->issue($this->copy(), $borrower);

        $this->expectException(InvalidValueException::class);

        app(IssueLoan::class)->issue($this->copy(), $borrower);
    }

    public function test_staff_may_hold_more_than_a_learner(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create([
            'school_id' => $this->workingSchool()->id,
            'learner_limit' => 1,
            'staff_limit' => 5,
        ]);
        $staff = $this->memberOf($this->workingSchool());
        $issue = app(IssueLoan::class);

        $issue->issue($this->copy(), $staff);
        $issue->issue($this->copy(), $staff);

        $this->assertSame(2, LibraryLoan::where('user_id', $staff->id)->open()->count());
    }

    public function test_a_returned_copy_goes_back_on_the_shelf(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $loan = app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));

        app(ReturnLoan::class)->receive($loan);

        $this->assertFalse($copy->fresh()->isOut());
        $this->assertTrue($copy->fresh()->canBeLent());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LibraryLoanReturned)->first());
    }

    public function test_a_reservation_holds_a_returned_copy_for_the_next_person(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $firstBorrower = $this->memberOf($this->workingSchool());
        $secondBorrower = $this->memberOf($this->workingSchool());
        $loan = app(IssueLoan::class)->issue($copy, $firstBorrower);

        $reservation = app(ReserveTitle::class)->reserve($copy->title, $secondBorrower);

        $this->assertSame(LibraryReservationStatus::Waiting, $reservation->status);

        app(ReturnLoan::class)->receive($loan);

        $reservation = $reservation->fresh();
        $this->assertSame(LibraryReservationStatus::Ready, $reservation->status);
        $this->assertSame($copy->id, $reservation->library_copy_id);

        $collected = app(IssueLoan::class)->issue($copy->fresh(), $secondBorrower);

        $this->assertSame($secondBorrower->id, $collected->user_id);
        $this->assertSame(LibraryReservationStatus::Collected, $reservation->fresh()->status);
    }

    public function test_a_reserved_copy_cannot_be_given_to_somebody_else(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $firstBorrower = $this->memberOf($this->workingSchool());
        $secondBorrower = $this->memberOf($this->workingSchool());
        $otherBorrower = $this->memberOf($this->workingSchool());
        $loan = app(IssueLoan::class)->issue($copy, $firstBorrower);
        app(ReserveTitle::class)->reserve($copy->title, $secondBorrower);
        app(ReturnLoan::class)->receive($loan);

        $this->expectException(InvalidValueException::class);

        app(IssueLoan::class)->issue($copy->fresh(), $otherBorrower);
    }

    public function test_the_lending_desk_can_add_somebody_to_the_library_queue(): void
    {
        $actor = $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());

        $actor->post(route('library-reservations.store'), [
            'library_title_id' => $copy->library_title_id,
            'user_id' => $borrower->id,
        ])->assertRedirect();

        $this->assertSame(1, LibraryReservation::query()->count());
    }

    public function test_a_librarian_can_lend_a_title_to_every_attending_learner_in_a_section(): void
    {
        $this->authorized_user([]);
        $school = $this->workingSchool();
        $section = AcademicCycleSection::factory()->create(['school_id' => $school->id]);
        $first = StudentRecord::factory()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $section->id,
        ]);
        $second = StudentRecord::factory()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $section->id,
        ]);
        $this->memberOf($school, $first->user);
        $this->memberOf($school, $second->user);
        $title = LibraryTitle::factory()->create();
        LibraryCopy::factory()->create(['school_id' => $school->id, 'library_title_id' => $title->id]);
        LibraryCopy::factory()->create(['school_id' => $school->id, 'library_title_id' => $title->id]);

        $loans = app(IssueTitleToSection::class)->issue($section, $title);

        $this->assertCount(2, $loans);
        $this->assertSame(
            [$first->user_id, $second->user_id],
            $loans->pluck('user_id')->all(),
        );
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LibrarySectionLoansIssued)->first());
    }

    public function test_a_class_set_does_not_partially_lend_when_copies_are_missing(): void
    {
        $this->authorized_user([]);
        $school = $this->workingSchool();
        $section = AcademicCycleSection::factory()->create(['school_id' => $school->id]);
        $first = StudentRecord::factory()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $section->id,
        ]);
        $second = StudentRecord::factory()->create([
            'school_id' => $school->id,
            'academic_cycle_section_id' => $section->id,
        ]);
        $this->memberOf($school, $first->user);
        $this->memberOf($school, $second->user);
        $title = LibraryTitle::factory()->create();
        LibraryCopy::factory()->create(['school_id' => $school->id, 'library_title_id' => $title->id]);

        $this->expectException(InvalidValueException::class);

        try {
            app(IssueTitleToSection::class)->issue($section, $title);
        } finally {
            $this->assertSame(0, LibraryLoan::count());
        }
    }

    public function test_a_copy_cannot_come_back_twice(): void
    {
        $this->authorized_user([]);
        $loan = app(IssueLoan::class)->issue($this->copy(), $this->memberOf($this->workingSchool()));
        app(ReturnLoan::class)->receive($loan);

        $this->expectException(InvalidValueException::class);

        app(ReturnLoan::class)->receive($loan->fresh());
    }

    public function test_a_late_book_charges_the_learner_through_the_ledger(): void
    {
        $this->authorized_user([]);
        FinancialPeriod::query()->firstOrCreate(
            [
                'school_id' => $this->workingSchool()->id,
                'name' => 'Current finance period',
            ],
            [
                'starts_on' => now()->startOfYear()->toDateString(),
                'ends_on' => now()->endOfYear()->toDateString(),
            ],
        );
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'fine_per_day' => 5_000]);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $borrower = $this->memberOf($this->workingSchool(), $enrollment->user);
        $loan = app(IssueLoan::class)->issue($this->copy(), $borrower, issuedOn: now()->subDays(20));

        $returned = app(ReturnLoan::class)->receive($loan);

        // Six days late at fifty a day.
        $this->assertSame(6, $returned->daysLate());
        $this->assertSame(30_000, $returned->fine_charged);
        $this->assertSame(300.0, app(StudentLedger::class)->balance($enrollment->fresh()));
    }

    public function test_a_late_book_costs_nothing_when_the_campus_charges_nothing(): void
    {
        $this->authorized_user([]);
        $loan = app(IssueLoan::class)->issue(
            $this->copy(),
            $this->memberOf($this->workingSchool()),
            issuedOn: now()->subDays(30),
        );

        $returned = app(ReturnLoan::class)->receive($loan);

        $this->assertSame(0, $returned->fine_charged);
    }

    public function test_a_member_of_staff_is_not_charged_a_fine(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'fine_per_day' => 5_000]);
        $loan = app(IssueLoan::class)->issue(
            $this->copy(),
            $this->memberOf($this->workingSchool()),
            issuedOn: now()->subDays(20),
        );

        $returned = app(ReturnLoan::class)->receive($loan);

        // The loan still records what was owed; nobody has an account to bill.
        $this->assertSame(30_000, $returned->fine_charged);
        $this->assertSame(0, LedgerTransaction::count());
    }

    public function test_a_loan_is_renewed_only_as_often_as_the_campus_allows(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'renewals_allowed' => 1]);
        $loan = app(IssueLoan::class)->issue($this->copy(), $this->memberOf($this->workingSchool()));
        $renew = app(RenewLoan::class);

        $renewed = $renew->renew($loan);

        $this->assertSame(1, $renewed->renewals);
        $this->assertSame(now()->addDays(28)->toDateString(), $renewed->due_on->toDateString());

        $this->expectException(InvalidValueException::class);

        $renew->renew($renewed->fresh());
    }

    public function test_a_late_copy_is_not_renewed(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'renewals_allowed' => 3]);
        $loan = app(IssueLoan::class)->issue(
            $this->copy(),
            $this->memberOf($this->workingSchool()),
            issuedOn: now()->subDays(20),
        );

        $this->expectException(InvalidValueException::class);

        app(RenewLoan::class)->renew($loan);
    }

    public function test_the_catalogue_is_shared_but_the_copies_are_not(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $elsewhere = School::factory()->create();
        LibraryCopy::factory()->create(['school_id' => $elsewhere->id, 'library_title_id' => $copy->library_title_id]);

        $this->assertSame(1, LibraryCopy::inSchool()->count());
        $this->assertSame(2, LibraryCopy::where('library_title_id', $copy->library_title_id)->count());
    }

    public function test_the_screens_are_hidden_until_the_school_turns_the_library_on(): void
    {
        $actor = $this->authorized_user(['read library']);

        $actor->get(route('library-copies.index'))->assertNotFound();

        app(FeatureManager::class)->enable(Feature::Library);

        $actor->get(route('library-copies.index'))->assertOk()->assertSee('What this campus owns');
    }

    public function test_the_librarian_can_shelve_several_copies_at_once(): void
    {
        $actor = $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);

        $actor->post(route('library-copies.store'), [
            'title' => 'Things Fall Apart',
            'authors' => 'Chinua Achebe',
            'barcode' => 'LIB-1000',
            'copies' => 3,
        ])->assertRedirect();

        $this->assertSame(3, LibraryCopy::inSchool()->count());
        $this->assertSame(1, LibraryTitle::where('title', 'Things Fall Apart')->count());
    }

    public function test_the_desk_lends_and_takes_back_from_the_screen(): void
    {
        $actor = $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());

        $actor->post(route('library-loans.store'), [
            'barcode' => $copy->barcode,
            'user_id' => $borrower->id,
        ])->assertRedirect();

        $loan = LibraryLoan::sole();
        $this->assertTrue($loan->isOpen());

        $actor->put(route('library-loans.update', $loan->id), ['do' => 'return'])->assertRedirect();

        $this->assertFalse($loan->fresh()->isOpen());
    }

    public function test_reading_the_library_does_not_allow_lending(): void
    {
        $actor = $this->authorized_user(['read library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();

        $actor->post(route('library-loans.store'), [
            'barcode' => $copy->barcode,
            'user_id' => $this->memberOf($this->workingSchool())->id,
        ])->assertForbidden();

        $this->assertSame(0, LibraryLoan::count());
    }

    public function test_the_campus_can_change_how_long_it_lends_for(): void
    {
        $actor = $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);

        $actor->put(route('library-rules.update'), [
            'loan_days' => 7,
            'learner_limit' => 2,
            'staff_limit' => 20,
            'renewals_allowed' => 0,
            'hold_days' => 5,
            'fine_per_day' => 25.50,
        ])->assertRedirect();

        $rules = LibraryLendingRules::forSchool();
        $this->assertSame(7, $rules->loan_days);
        $this->assertSame(2_550, $rules->fine_per_day);
        $this->assertSame(5, $rules->hold_days);
    }

    /**
     * Put one copy on this campus's shelf.
     */
    private function copy(): LibraryCopy
    {
        return LibraryCopy::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
