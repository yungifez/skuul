<?php

namespace Tests\Feature;

use App\Actions\Library\CloseReservation;
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
use App\Livewire\LibraryCopyCatalog as LibraryCopyCatalogComponent;
use App\Livewire\LibraryLendingDesk;
use App\Livewire\LibraryLendingRulesForm;
use App\Livewire\LibraryReservationQueue;
use App\Livewire\LibraryShelvingForm;
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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());

        Livewire::test(LibraryReservationQueue::class)
            ->call('startAdding')
            ->set('titleId', (string) $copy->library_title_id)
            ->set('borrowerSearch', $borrower->name)
            ->assertSee($borrower->name)
            ->call('reserveFor', $borrower->id)
            ->assertHasNoErrors()
            ->assertSet('isAdding', false)
            ->assertSee('Behind the desk');

        $this->assertSame(LibraryReservationStatus::Ready, LibraryReservation::sole()->status);

        Livewire::test(LibraryReservationQueue::class)
            ->call('startAdding')
            ->set('titleId', (string) $copy->library_title_id)
            ->call('reserveFor', $borrower->id)
            ->assertHasErrors('borrowerSearch')
            ->assertSee('This person is already in the queue for this title.');

        $this->assertSame(1, LibraryReservation::query()->count());
    }

    public function test_the_queue_takes_nobody_and_nothing_from_another_campus(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $otherSchool = School::factory()->create();
        $copy = $this->copy();
        $outsider = $this->memberOf($otherSchool, $this->nonMember());
        $foreignCopy = LibraryCopy::factory()->create(['school_id' => $otherSchool->id]);
        $foreignReservation = app(ReserveTitle::class)->reserve($foreignCopy->title, $this->memberOf($otherSchool, $this->nonMember()), schoolId: $otherSchool->id);

        Livewire::test(LibraryReservationQueue::class)
            ->assertDontSee($foreignCopy->title->title)
            ->call('startAdding')
            ->set('titleId', (string) $copy->library_title_id)
            ->set('borrowerSearch', $outsider->name)
            ->assertSee('Nobody on this campus matches')
            ->call('reserveFor', $outsider->id)
            ->assertHasErrors('borrowerSearch')
            ->set('titleId', (string) $foreignCopy->library_title_id)
            ->call('reserveFor', $this->memberOf($this->workingSchool())->id)
            ->assertHasErrors('titleId');

        $this->assertThrows(fn () => Livewire::test(LibraryReservationQueue::class)
            ->call('takeOff', $foreignReservation->id), ModelNotFoundException::class);

        $this->assertSame(1, LibraryReservation::query()->count());
        $this->assertTrue($foreignReservation->fresh()->isOpen());
    }

    public function test_a_stale_screen_cannot_take_off_a_reservation_that_was_collected(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());
        $reservation = app(ReserveTitle::class)->reserve($copy->title, $borrower);

        $stale = Livewire::test(LibraryReservationQueue::class)->assertSee($borrower->name);
        app(IssueLoan::class)->issue($copy->fresh(), $borrower);

        $stale->call('takeOff', $reservation->id)
            ->assertDispatched('status-message', type: 'danger', message: 'This reservation has already ended.');

        $reservation = $reservation->fresh();
        $this->assertSame(LibraryReservationStatus::Collected, $reservation->status);
        $this->assertSame($copy->id, $reservation->library_copy_id);
    }

    public function test_the_nightly_run_leaves_a_hold_collected_after_it_was_listed(): void
    {
        $this->authorized_user([]);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());
        $reservation = app(ReserveTitle::class)->reserve($copy->title, $borrower);
        app(IssueLoan::class)->issue($copy->fresh(), $borrower);

        try {
            app(CloseReservation::class)->expire($reservation);
            $this->fail('A collected hold was expired.');
        } catch (InvalidValueException $exception) {
            $this->assertSame('This reservation has already ended.', $exception->getMessage());
        }

        $this->assertSame(LibraryReservationStatus::Collected, $reservation->fresh()->status);
        $this->assertSame($copy->id, $reservation->fresh()->library_copy_id);
    }

    public function test_a_withdrawn_copy_passes_its_hold_to_another_copy(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());
        $reservation = app(ReserveTitle::class)->reserve($copy->title, $borrower);
        $this->assertSame($copy->id, $reservation->library_copy_id);

        $spare = LibraryCopy::factory()->create(['school_id' => $copy->school_id, 'library_title_id' => $copy->library_title_id]);

        Livewire::test(LibraryCopyCatalogComponent::class)
            ->call('withdraw', $copy->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertSame(LibraryCopyStatus::Withdrawn, $copy->fresh()->status);
        $this->assertSame(LibraryReservationStatus::Ready, $reservation->fresh()->status);
        $this->assertSame($spare->id, $reservation->fresh()->library_copy_id);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::LibraryCopyWithdrawn)->first());
    }

    public function test_the_last_copy_withdrawn_puts_its_reader_back_at_the_front_of_the_queue(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $first = app(ReserveTitle::class)->reserve($copy->title, $this->memberOf($this->workingSchool()));
        $second = app(ReserveTitle::class)->reserve($copy->title, $this->memberOf($this->workingSchool()));

        Livewire::test(LibraryCopyCatalogComponent::class)->call('withdraw', $copy->id);

        $first = $first->fresh();
        $this->assertSame(LibraryReservationStatus::Waiting, $first->status);
        $this->assertNull($first->library_copy_id);
        $this->assertNull($first->holds_until);
        $this->assertSame(1, $first->placeInQueue());
        $this->assertSame(2, $second->fresh()->placeInQueue());
    }

    public function test_a_copy_somebody_has_or_another_campus_owns_is_not_withdrawn(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        app(IssueLoan::class)->issue($copy, $this->memberOf($this->workingSchool()));
        $foreign = LibraryCopy::factory()->create(['school_id' => School::factory()->create()->id]);

        Livewire::test(LibraryCopyCatalogComponent::class)
            ->call('withdraw', $copy->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Somebody has this copy. Take it back first.');

        $this->assertThrows(fn () => Livewire::test(LibraryCopyCatalogComponent::class)
            ->call('withdraw', $foreign->id), ModelNotFoundException::class);

        $this->assertSame(LibraryCopyStatus::OnShelf, $copy->fresh()->status);
        $this->assertSame(LibraryCopyStatus::OnShelf, $foreign->fresh()->status);
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

        $actor->get(route('library-copies.index'))->assertOk()->assertSee('on the shelf')->assertSeeLivewire(LibraryCopyCatalogComponent::class);
    }

    public function test_the_library_catalogue_searches_copies_live_and_clears_the_search(): void
    {
        $this->authorized_user(['read library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $matchingTitle = LibraryTitle::factory()->create(['title' => 'The River Between']);
        $matchingCopy = LibraryCopy::factory()->create([
            'school_id' => $this->workingSchool()->id,
            'library_title_id' => $matchingTitle->id,
            'barcode' => 'BOOK-104',
        ]);
        $otherCopy = $this->copy();

        Livewire::test(LibraryCopyCatalogComponent::class)
            ->assertSee($matchingCopy->barcode)
            ->assertSee($otherCopy->barcode)
            ->set('search', 'River Between')
            ->assertSee($matchingCopy->barcode)
            ->assertDontSee($otherCopy->barcode)
            ->set('search', 'No such title')
            ->assertSee('Nothing matches that search.')
            ->call('clearSearch')
            ->assertSee($matchingCopy->barcode)
            ->assertSee($otherCopy->barcode);
    }

    public function test_the_librarian_can_shelve_several_copies_at_once(): void
    {
        $actor = $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);

        $actor->get(route('library-copies.index'))->assertOk()->assertSeeLivewire(LibraryShelvingForm::class);

        Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->set('titleSearch', 'Things Fall Apart')
            ->call('describeNew')
            ->assertSet('title', 'Things Fall Apart')
            ->set('authors', 'Chinua Achebe')
            ->set('isbn', '9780385474542')
            ->set('barcode', 'LIB-1000')
            ->set('copies', '3')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isShelving', false)
            ->assertDispatched('library-copies-shelved');

        $this->assertSame(['LIB-1000', 'LIB-1000-1', 'LIB-1000-2'], LibraryCopy::inSchool()->orderBy('id')->pluck('barcode')->all());
        $this->assertSame(1, LibraryTitle::where('title', 'Things Fall Apart')->count());

        $title = LibraryTitle::where('title', 'Things Fall Apart')->sole();

        Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->set('titleSearch', 'Achebe')
            ->assertSee('Things Fall Apart')
            ->call('pickTitle', $title->id)
            ->set('barcode', 'LIB-2000')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(4, $title->copies()->count());
    }

    public function test_a_numbered_barcode_that_clashes_shelves_nothing_and_says_which(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = LibraryCopy::factory()->create(['school_id' => $this->workingSchool()->id, 'barcode' => 'BOX-2']);

        Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->call('pickTitle', $copy->library_title_id)
            ->set('barcode', 'BOX')
            ->set('copies', '4')
            ->call('save')
            ->assertHasErrors('barcode')
            ->assertSee('This campus already has a copy with the barcode BOX-2.');

        $this->assertSame(1, LibraryCopy::query()->count());
    }

    public function test_the_same_isbn_is_not_described_twice(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        LibraryTitle::factory()->create(['organization_id' => $this->workingSchool()->organization_id, 'title' => 'Arrow of God', 'isbn' => '9780385014809']);

        Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->call('describeNew')
            ->set('title', 'Arrow of God (2nd)')
            ->set('isbn', '9780385014809')
            ->set('barcode', 'AOG-1')
            ->call('save')
            ->assertHasErrors('isbn')
            ->assertSee('The catalogue already has this ISBN as');

        $this->assertSame(1, LibraryTitle::query()->count());
        $this->assertSame(0, LibraryCopy::query()->count());
    }

    public function test_a_book_of_another_school_group_cannot_be_picked(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $foreign = LibraryTitle::factory()->create(['organization_id' => School::factory()->create()->organization_id, 'title' => 'Private reader']);

        $this->assertThrows(fn () => Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->set('titleSearch', 'Private reader')
            ->assertDontSee('Private reader')
            ->call('pickTitle', $foreign->id), ModelNotFoundException::class);

        Livewire::test(LibraryShelvingForm::class)
            ->call('start')
            ->set('barcode', 'X-1')
            ->call('save')
            ->assertHasErrors('titleSearch');

        $this->assertSame(0, LibraryCopy::query()->count());
    }

    public function test_a_reader_cannot_shelve(): void
    {
        $this->authorized_user(['read library']);
        app(FeatureManager::class)->enable(Feature::Library);

        Livewire::test(LibraryShelvingForm::class)->assertForbidden();
    }

    public function test_the_desk_lends_and_takes_back_from_the_screen(): void
    {
        $actor = $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id, 'admission_number' => 'ADM-7781']);
        $borrower = $this->memberOf($this->workingSchool(), $enrollment->user);

        $actor->get(route('library-loans.index'))->assertOk()->assertSeeLivewire(LibraryLendingDesk::class);

        $desk = Livewire::test(LibraryLendingDesk::class)
            ->set('barcode', $copy->barcode)
            ->call('scan')
            ->set('borrowerSearch', '7781')
            ->assertSee($borrower->name)
            ->call('lendTo', $borrower->id)
            ->assertHasNoErrors()
            ->assertSet('scannedCopyId', null);

        $loan = LibraryLoan::sole();
        $this->assertSame($borrower->id, $loan->user_id);

        $desk->set('barcode', $copy->barcode)
            ->call('scan')
            ->assertSee('Take it back')
            ->assertDontSee('Who is taking it?')
            ->call('takeBack', $loan->id)
            ->assertDispatched('status-message', type: 'success', message: 'The copy is back.');

        $this->assertFalse($loan->fresh()->isOpen());
    }

    public function test_the_desk_finds_no_copy_or_person_of_another_campus(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $otherSchool = School::factory()->create();
        $foreignCopy = LibraryCopy::factory()->create(['school_id' => $otherSchool->id]);
        $outsider = $this->memberOf($otherSchool, $this->nonMember());
        $copy = $this->copy();

        Livewire::test(LibraryLendingDesk::class)
            ->set('barcode', $foreignCopy->barcode)
            ->call('scan')
            ->assertHasErrors('barcode')
            ->assertSee('No copy on this campus has that barcode.')
            ->set('barcode', $copy->barcode)
            ->call('scan')
            ->assertHasNoErrors()
            ->set('borrowerSearch', $outsider->name)
            ->assertSee('Nobody on this campus matches')
            ->call('lendTo', $outsider->id)
            ->assertHasErrors('borrowerSearch')
            ->assertSee('This person does not belong to this campus.');

        $this->assertSame(0, LibraryLoan::count());
    }

    public function test_a_second_tap_on_take_it_back_charges_no_second_fine(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        FinancialPeriod::query()->firstOrCreate(
            ['school_id' => $this->workingSchool()->id, 'name' => 'Current finance period'],
            ['starts_on' => now()->startOfYear()->toDateString(), 'ends_on' => now()->endOfYear()->toDateString()],
        );
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'fine_per_day' => 5_000]);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $borrower = $this->memberOf($this->workingSchool(), $enrollment->user);
        $loan = app(IssueLoan::class)->issue($this->copy(), $borrower, issuedOn: now()->subDays(20));

        $firstTab = Livewire::test(LibraryLendingDesk::class);
        $secondTab = Livewire::test(LibraryLendingDesk::class);

        $firstTab->call('takeBack', $loan->id)->assertDispatched('status-message', type: 'success');
        $secondTab->call('takeBack', $loan->id)->assertDispatched('status-message', type: 'danger', message: 'This copy is already back.');

        // The loan object the second return holds still reads as open.
        try {
            app(ReturnLoan::class)->receive($loan);
            $this->fail('A stale loan came back twice.');
        } catch (InvalidValueException) {
        }

        $this->assertSame(300.0, app(StudentLedger::class)->balance($enrollment->fresh()));
    }

    public function test_a_stale_loan_is_not_renewed_past_the_limit(): void
    {
        $this->authorized_user([]);
        LibraryLendingRules::create(['school_id' => $this->workingSchool()->id, 'renewals_allowed' => 1]);
        $loan = app(IssueLoan::class)->issue($this->copy(), $this->memberOf($this->workingSchool()));
        app(RenewLoan::class)->renew($loan);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('This loan has been renewed as often as the library allows.');

        try {
            app(RenewLoan::class)->renew($loan);
        } finally {
            $this->assertSame(1, $loan->fresh()->renewals);
        }
    }

    public function test_the_desk_lends_a_class_set_or_says_why_not(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $school = $this->workingSchool();
        $section = AcademicCycleSection::factory()->create(['school_id' => $school->id]);
        foreach (range(1, 2) as $learner) {
            $enrollment = StudentRecord::factory()->create(['school_id' => $school->id, 'academic_cycle_section_id' => $section->id]);
            $this->memberOf($school, $enrollment->user);
        }
        $title = LibraryTitle::factory()->create();
        LibraryCopy::factory()->create(['school_id' => $school->id, 'library_title_id' => $title->id]);

        $desk = Livewire::test(LibraryLendingDesk::class)
            ->call('$set', 'isLendingSet', true)
            ->set('sectionId', (string) $section->id)
            ->set('titleId', (string) $title->id)
            ->call('lendSet')
            ->assertHasErrors('sectionId');

        $this->assertSame(0, LibraryLoan::count());

        LibraryCopy::factory()->create(['school_id' => $school->id, 'library_title_id' => $title->id]);

        $desk->call('lendSet')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success', message: "2 copies of {$title->title} are out to the class.");

        $this->assertSame(2, LibraryLoan::count());
    }

    public function test_reading_the_library_does_not_allow_lending(): void
    {
        $this->authorized_user(['read library']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();

        Livewire::test(LibraryLendingDesk::class)
            ->assertDontSee('Scan a copy')
            ->set('barcode', $copy->barcode)
            ->call('scan')
            ->call('lendTo', $this->memberOf($this->workingSchool())->id)
            ->assertForbidden();

        $this->assertSame(0, LibraryLoan::count());
    }

    public function test_the_campus_can_change_how_long_it_lends_for(): void
    {
        $actor = $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);

        $actor->get(route('library-rules.edit'))->assertOk()->assertSeeLivewire(LibraryLendingRulesForm::class);

        Livewire::test(LibraryLendingRulesForm::class)
            ->assertSet('loanDays', '14')
            ->set('loanDays', '7')
            ->set('learnerLimit', '2')
            ->set('staffLimit', '20')
            ->set('renewalsAllowed', '0')
            ->set('holdDays', '5')
            ->set('finePerDay', '25.50')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('status-message', type: 'success');

        $rules = LibraryLendingRules::forSchool();
        $this->assertSame(7, $rules->loan_days);
        $this->assertSame(2_550, $rules->fine_per_day);
        $this->assertSame(5, $rules->hold_days);

        Livewire::test(LibraryLendingRulesForm::class)
            ->assertSet('loanDays', '7')
            ->assertSet('finePerDay', '25.50')
            ->set('holdDays', '6')
            ->call('save');

        $this->assertSame(1, LibraryLendingRules::query()->count());
        $this->assertSame(6, LibraryLendingRules::forSchool()->hold_days);
    }

    public function test_a_fine_smaller_than_the_currency_allows_is_refused_not_crashed(): void
    {
        $this->authorized_user(['read library', 'manage library']);
        app(FeatureManager::class)->enable(Feature::Library);

        Livewire::test(LibraryLendingRulesForm::class)
            ->set('finePerDay', '10.005')
            ->call('save')
            ->assertHasErrors(['finePerDay' => 'decimal'])
            ->set('finePerDay', '-1')
            ->call('save')
            ->assertHasErrors(['finePerDay' => 'min'])
            ->set('loanDays', '0')
            ->call('save')
            ->assertHasErrors(['loanDays' => 'min']);

        $this->assertSame(0, LibraryLendingRules::query()->count());
    }

    public function test_a_reader_cannot_change_the_lending_rules(): void
    {
        $this->authorized_user(['read library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);

        Livewire::test(LibraryLendingRulesForm::class)->assertForbidden();
    }

    /**
     * Put one copy on this campus's shelf.
     */
    public function test_the_library_screens_say_what_each_destructive_button_really_does(): void
    {
        $actor = $this->authorized_user(['read library', 'manage library', 'lend library item']);
        app(FeatureManager::class)->enable(Feature::Library);
        $copy = $this->copy();
        $borrower = $this->memberOf($this->workingSchool());
        app(ReserveTitle::class)->reserve($copy->title, $borrower);

        // Without its own wording, the shared handler warns that the record is
        // being deleted. Neither button deletes anything the reader can see.
        $actor->get(route('library-copies.index'))->assertOk()
            ->assertSee('wire:confirm="Withdraw this copy from the shelves?"', false);

        $actor->get(route('library-reservations.index'))->assertOk()
            ->assertSee('wire:confirm="Take this reservation off the queue? The copy goes to the next person."', false);
    }

    private function copy(): LibraryCopy
    {
        return LibraryCopy::factory()->create(['school_id' => $this->workingSchool()->id]);
    }
}
