<?php

namespace Tests\Feature;

use App\Actions\Admissions\AcceptWaitlistEntry;
use App\Actions\Admissions\JoinWaitlist;
use App\Actions\Admissions\OfferNextWaitlistEntry;
use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\AdmissionWaitlistStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\AdmissionWaitlistBoard;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AdmissionWaitlistEntry;
use App\Models\AuditEvent;
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

    public function test_staff_can_read_the_waitlist_screen(): void
    {
        $this->authorized_user(['read admission waitlist'])
            ->get(route('admissions.waitlist.index'))
            ->assertOk()
            ->assertSee('Admissions waitlist');
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
