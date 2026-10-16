<?php

namespace Tests\Feature;

use App\Actions\Admissions\AcceptWaitlistEntry;
use App\Actions\Admissions\JoinWaitlist;
use App\Actions\Admissions\OfferNextWaitlistEntry;
use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Livewire\AdmissionWaitlistBoard;
use App\Livewire\Layouts\Menu;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AdmissionWaitlistEntry;
use App\Models\AuditEvent;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdmissionsTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_section_cannot_accept_more_active_learners_than_its_capacity(): void
    {
        $section = $this->section(1);
        $first = $this->unplacedStudent();
        $second = $this->unplacedStudent();

        app(ChangeEnrollmentPlacement::class)->place($first, $section);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Add the candidate to its admission waitlist instead.');

        app(ChangeEnrollmentPlacement::class)->place($second, $section);
    }

    public function test_a_suspended_learner_keeps_their_seat(): void
    {
        $section = $this->section(1);
        $suspended = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($suspended, $section);
        $suspended->update(['status' => EnrollmentStatus::Suspended]);
        app(JoinWaitlist::class)->join($section, User::factory()->create());

        $this->assertNull(app(OfferNextWaitlistEntry::class)->offer($section));

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Add the candidate to its admission waitlist instead.');

        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
    }

    public function test_a_candidate_the_school_enrolled_while_waiting_is_passed_over(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $enrolledMeanwhile = User::factory()->create();
        $stillLooking = User::factory()->create();
        $first = app(JoinWaitlist::class)->join($section, $enrolledMeanwhile, priority: 10);
        $second = app(JoinWaitlist::class)->join($section, $stillLooking, priority: 1);
        StudentRecord::factory()->create([
            'user_id' => $enrolledMeanwhile->id,
            'school_id' => $section->school_id,
        ]);

        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        $this->assertSame($second->id, $offered?->id);
        $this->assertSame(AdmissionWaitlistStatus::Withdrawn, $first->fresh()->status);
        $this->assertStringContainsString('while waiting', $first->fresh()->decision_reason);
    }

    public function test_a_full_section_keeps_one_idempotent_waitlist_entry(): void
    {
        $section = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
        $candidate = User::factory()->create();

        $first = app(JoinWaitlist::class)->join($section, $candidate, priority: 4);
        $second = app(JoinWaitlist::class)->join($section, $candidate, priority: 9);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(AdmissionWaitlistStatus::Pending, $first->fresh()->status);
        $this->assertSame(4, $first->fresh()->priority);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AdmissionWaitlistJoined)->forSubject($first)->first());
    }

    public function test_the_highest_priority_candidate_is_offered_and_can_accept_a_place(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);

        $low = User::factory()->create();
        $high = User::factory()->create();
        app(JoinWaitlist::class)->join($section, $low, priority: 1);
        $highEntry = app(JoinWaitlist::class)->join($section, $high, priority: 10);

        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        $this->assertNotNull($offered);
        $this->assertSame($highEntry->id, $offered->id);
        $this->assertSame(AdmissionWaitlistStatus::Offered, $offered->status);

        $enrollment = app(AcceptWaitlistEntry::class)->accept($offered);

        $this->assertSame($high->id, $enrollment->user_id);
        $this->assertSame($section->id, $enrollment->fresh()->academic_cycle_section_id);
        $this->assertSame(AdmissionWaitlistStatus::Placed, $offered->fresh()->status);
        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
    }

    public function test_a_learner_who_left_takes_an_offer_back_into_their_own_enrollment(): void
    {
        $oldSection = $this->section(1);
        $returning = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($returning, $oldSection);
        app(ChangeEnrollmentStatus::class)->change($returning, EnrollmentStatus::Withdrawn);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $oldSection);

        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        app(JoinWaitlist::class)->join($section, $returning->user);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        $this->assertNotNull($offered);

        $enrollment = app(AcceptWaitlistEntry::class)->accept($offered);

        $this->assertSame($returning->id, $enrollment->id);
        $this->assertSame($returning->admission_number, $enrollment->fresh()->admission_number);
        $this->assertSame(EnrollmentStatus::Active, $enrollment->fresh()->status);
        $this->assertSame($section->id, $enrollment->fresh()->academic_cycle_section_id);
        $this->assertSame(AdmissionWaitlistStatus::Placed, $offered->fresh()->status);
        $this->assertSame(1, StudentRecord::query()->where('user_id', $returning->user_id)->count());
    }

    public function test_an_offer_to_a_learner_whose_enrollment_cannot_reopen_is_refused(): void
    {
        $transferred = $this->unplacedStudent();
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        app(ChangeEnrollmentStatus::class)->change($transferred, EnrollmentStatus::Transferred);
        app(JoinWaitlist::class)->join($section, $transferred->user);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        $this->assertNotNull($offered);

        try {
            app(AcceptWaitlistEntry::class)->accept($offered);
            $this->fail('A transferred enrollment was opened again.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('cannot be opened again', $exception->getMessage());
        }

        $this->assertSame(AdmissionWaitlistStatus::Offered, $offered->fresh()->status);
        $this->assertSame(EnrollmentStatus::Transferred, $transferred->fresh()->status);
    }

    public function test_only_a_learner_who_left_can_be_taken_back_into_a_section(): void
    {
        $attending = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($attending, $this->section(2));

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Only a learner who left can be taken back into a section.');

        app(ChangeEnrollmentStatus::class)->change($attending, EnrollmentStatus::Suspended, into: $this->section(2));
    }

    public function test_an_offer_to_a_learner_placed_another_way_frees_its_seat(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $leaver = $this->unplacedStudent();
        app(ChangeEnrollmentStatus::class)->change($leaver, EnrollmentStatus::Withdrawn);
        app(JoinWaitlist::class)->join($section, $leaver->user, priority: 10);
        $next = app(JoinWaitlist::class)->join($section, User::factory()->create(), priority: 1);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        app(ChangeEnrollmentStatus::class)->returnToAttendance($leaver);
        app(ChangeEnrollmentPlacement::class)->place($leaver, $this->section(1));

        $this->assertSame(AdmissionWaitlistStatus::Withdrawn, $offered?->fresh()->status);
        $this->assertSame($next->id, app(OfferNextWaitlistEntry::class)->offer($section)?->id);
    }

    public function test_one_free_seat_is_offered_to_one_family(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        app(JoinWaitlist::class)->join($section, User::factory()->create(), priority: 10);
        app(JoinWaitlist::class)->join($section, User::factory()->create(), priority: 1);

        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $first = app(OfferNextWaitlistEntry::class)->offer($section);
        $second = app(OfferNextWaitlistEntry::class)->offer($section);

        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertSame(1, AdmissionWaitlistEntry::where('academic_cycle_section_id', $section->id)->where('status', AdmissionWaitlistStatus::Offered)->count());
    }

    public function test_the_queue_is_still_offered_after_the_limit_is_lifted(): void
    {
        $section = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
        $waiting = app(JoinWaitlist::class)->join($section, User::factory()->create());

        $section->forceFill(['capacity' => null])->save();

        $this->assertSame($waiting->id, app(OfferNextWaitlistEntry::class)->offer($section)?->id);
        $this->assertSame(AdmissionWaitlistStatus::Offered, $waiting->fresh()->status);
    }

    public function test_a_seat_held_by_an_offer_is_not_free_to_join(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        app(JoinWaitlist::class)->join($section, User::factory()->create());

        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        app(OfferNextWaitlistEntry::class)->offer($section);
        $late = app(JoinWaitlist::class)->join($section, User::factory()->create());

        $this->assertSame(AdmissionWaitlistStatus::Pending, $late->status);
    }

    public function test_a_seat_held_by_an_offer_cannot_be_filled_another_way(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        app(JoinWaitlist::class)->join($section, User::factory()->create());
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offer = app(OfferNextWaitlistEntry::class)->offer($section);

        try {
            app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
            $this->fail('A learner took the seat a waiting family was offered.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('open waitlist offers', $exception->getMessage());
        }

        $enrollment = app(AcceptWaitlistEntry::class)->accept($offer);

        $this->assertSame($section->id, $enrollment->fresh()->academic_cycle_section_id);
    }

    public function test_a_learner_attending_another_campus_cannot_take_a_place(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $candidate = User::factory()->create();
        $sibling = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id, 'name' => 'Hill Campus']);
        StudentRecord::factory()->create(['user_id' => $candidate->id, 'school_id' => $sibling->id, 'status' => EnrollmentStatus::Active]);
        app(JoinWaitlist::class)->join($section, $candidate);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        try {
            app(AcceptWaitlistEntry::class)->accept($offered);
            $this->fail('A learner attending another campus was admitted again.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('Hill Campus', $exception->getMessage());
        }

        $this->assertSame(1, StudentRecord::query()->where('user_id', $candidate->id)->count());
        $this->assertSame(AdmissionWaitlistStatus::Offered, $offered->fresh()->status);
    }

    public function test_a_learner_at_a_school_of_another_organization_is_refused_without_naming_it(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $candidate = User::factory()->create();
        $stranger = School::factory()->create(['name' => 'Far Academy']);
        StudentRecord::factory()->create(['user_id' => $candidate->id, 'school_id' => $stranger->id, 'status' => EnrollmentStatus::Active]);
        app(JoinWaitlist::class)->join($section, $candidate);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        try {
            app(AcceptWaitlistEntry::class)->accept($offered);
            $this->fail('A learner attending another school was admitted again.');
        } catch (InvalidValueException $exception) {
            $this->assertSame('This candidate is enrolled at another school. Ask that school to move or transfer them.', $exception->getMessage());
        }

        $this->assertSame(1, StudentRecord::query()->where('user_id', $candidate->id)->count());
    }

    public function test_a_member_of_staff_cannot_take_a_place(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $candidate = $this->memberOf($this->workingSchool());
        app(JoinWaitlist::class)->join($section, $candidate);
        $candidate->assignRole(Role::Teacher);
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $offered = app(OfferNextWaitlistEntry::class)->offer($section);

        try {
            app(AcceptWaitlistEntry::class)->accept($offered);
            $this->fail('A teacher was admitted as a learner.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('works as staff', $exception->getMessage());
        }

        $this->assertSame(0, StudentRecord::query()->where('user_id', $candidate->id)->count());
        $this->assertFalse($candidate->refresh()->hasRole(Role::Student->value));
    }

    public function test_staff_can_read_the_waitlist_screen(): void
    {
        $this->authorized_user(['read admission waitlist'])
            ->get(route('admissions.waitlist.index'))
            ->assertOk()
            ->assertSee('Admissions waitlist');
    }

    public function test_the_sidebar_links_the_waitlist_for_people_who_can_read_it(): void
    {
        $this->authorized_user(['read student']);
        Livewire::test(Menu::class)->assertDontSee(route('admissions.waitlist.index'));

        $this->authorized_user(['read admission waitlist']);
        Livewire::test(Menu::class)->assertSee(route('admissions.waitlist.index'));
    }

    public function test_livewire_waitlist_flow_adds_offers_and_enrols_a_candidate(): void
    {
        $school = $this->workingSchool();
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $candidate = $this->memberOf($school, User::factory()->create());
        $this->authorized_user(['read admission waitlist', 'manage admission waitlist']);

        $board = Livewire::test(AdmissionWaitlistBoard::class)
            ->set('academic_cycle_section_id', $section->id)
            ->set('user_id', $candidate->id)
            ->set('priority', 5)
            ->call('addCandidate')
            ->assertHasNoErrors();

        $entry = AdmissionWaitlistEntry::query()->where('user_id', $candidate->id)->firstOrFail();
        $this->assertSame(AdmissionWaitlistStatus::Pending, $entry->status);

        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $board->call('offer', $entry->id);
        $this->assertSame(AdmissionWaitlistStatus::Offered, $entry->fresh()->status);

        $board->call('accept', $entry->id)->assertHasNoErrors();
        $this->assertSame(AdmissionWaitlistStatus::Placed, $entry->fresh()->status);
        $this->assertDatabaseHas('student_records', [
            'user_id' => $candidate->id,
            'academic_cycle_section_id' => $section->id,
            'status' => EnrollmentStatus::Active->value,
        ]);
    }

    public function test_livewire_waitlist_refuses_nonmembers_and_candidates_for_an_open_section(): void
    {
        $school = $this->workingSchool();
        $section = $this->section(1);
        $nonMember = $this->nonMember();
        $this->authorized_user(['read admission waitlist', 'manage admission waitlist']);

        Livewire::test(AdmissionWaitlistBoard::class)
            ->set('academic_cycle_section_id', $section->id)
            ->set('user_id', $nonMember->id)
            ->call('addCandidate')
            ->assertHasErrors('user_id');

        $candidate = $this->memberOf($school, User::factory()->create());
        Livewire::test(AdmissionWaitlistBoard::class)
            ->set('academic_cycle_section_id', $section->id)
            ->set('user_id', $candidate->id)
            ->call('addCandidate')
            ->assertHasErrors('waitlist');
    }

    public function test_livewire_waitlist_can_decline_an_open_entry(): void
    {
        $section = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
        $candidate = $this->memberOf($this->workingSchool(), User::factory()->create());
        $entry = app(JoinWaitlist::class)->join($section, $candidate);
        $this->authorized_user(['read admission waitlist', 'manage admission waitlist']);

        Livewire::test(AdmissionWaitlistBoard::class)
            ->call('decline', $entry->id)
            ->assertHasNoErrors();

        $this->assertSame(AdmissionWaitlistStatus::Declined, $entry->fresh()->status);
    }

    public function test_a_member_of_staff_cannot_join_the_waitlist(): void
    {
        $section = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $section);
        $teacher = $this->memberOf($this->workingSchool(), User::factory()->create());
        $teacher->assignRole(Role::Teacher);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('works as staff');

        app(JoinWaitlist::class)->join($section, $teacher);
    }

    public function test_the_board_offers_only_full_sections_and_people_who_can_wait(): void
    {
        $school = $this->workingSchool();
        $full = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $full);
        $roomy = $this->section(5);
        $closed = $this->section(1);
        app(ChangeEnrollmentPlacement::class)->place($this->unplacedStudent(), $closed);
        $closed->academicYear->forceFill(['status' => AcademicPeriodStatus::Closed])->save();

        $family = $this->memberOf($school, User::factory()->create(['name' => 'Waiting Family']));
        $teacher = $this->memberOf($school, User::factory()->create(['name' => 'Staff Teacher']));
        $teacher->assignRole(Role::Teacher);
        $suspended = StudentRecord::factory()->create(['school_id' => $school->id, 'status' => EnrollmentStatus::Suspended])->user;
        $this->authorized_user(['read admission waitlist', 'manage admission waitlist']);
        auth()->user()->assignRole(Role::Admin);

        $view = Livewire::test(AdmissionWaitlistBoard::class)->viewData('sections');
        $this->assertSame([$full->id], $view->pluck('id')->all());

        $candidates = Livewire::test(AdmissionWaitlistBoard::class)->viewData('candidates')->pluck('id');
        $this->assertTrue($candidates->contains($family->id));
        $this->assertFalse($candidates->contains($teacher->id));
        $this->assertFalse($candidates->contains($suspended->id));
        $this->assertFalse($candidates->contains(auth()->id()));
    }

    public function test_only_the_next_candidate_in_a_section_has_an_offer_button(): void
    {
        $section = $this->section(1);
        $occupied = $this->unplacedStudent();
        app(ChangeEnrollmentPlacement::class)->place($occupied, $section);
        $first = app(JoinWaitlist::class)->join($section, $this->memberOf($this->workingSchool(), User::factory()->create()), priority: 1);
        $second = app(JoinWaitlist::class)->join($section, $this->memberOf($this->workingSchool(), User::factory()->create()));
        app(ChangeEnrollmentStatus::class)->graduate($occupied);
        $this->authorized_user(['read admission waitlist', 'manage admission waitlist']);

        $board = Livewire::test(AdmissionWaitlistBoard::class)
            ->assertViewHas('nextEntryIds', [$first->id])
            ->assertSeeHtml("offer({$first->id})")
            ->assertDontSeeHtml("offer({$second->id})")
            ->call('offer', $first->id)
            ->assertDispatched('status-message', type: 'success', message: "Offered a place to {$first->candidate->name}.");

        $this->assertSame(AdmissionWaitlistStatus::Offered, $first->fresh()->status);
        $board->assertDontSeeHtml("offer({$second->id})");
    }

    private function section(int $capacity): AcademicCycleSection
    {
        $school = $this->workingSchool();
        $year = AcademicYear::factory()->create(['school_id' => $school->id]);
        $level = AcademicLevel::factory()->create(['school_id' => $school->id]);

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'academic_level_id' => $level->id,
            'capacity' => $capacity,
            'status' => AcademicStructureStatus::Active,
        ]);
    }

    private function unplacedStudent(): StudentRecord
    {
        return StudentRecord::factory()->create([
            'academic_cycle_section_id' => null,
            'status' => EnrollmentStatus::Active,
        ]);
    }
}
