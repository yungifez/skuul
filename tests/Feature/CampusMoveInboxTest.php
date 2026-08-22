<?php

namespace Tests\Feature;

use App\Actions\Enrollment\RequestCampusMove;
use App\Enums\AcademicStructureStatus;
use App\Enums\CampusMoveStatus;
use App\Livewire\ListCampusMoveRequests;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CampusMoveRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Services\Authorization\CampusMoveAuthority;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The receiving campus decides the moves arriving at it.
 */
class CampusMoveInboxTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_receiving_campus_sees_the_request_and_approves_it(): void
    {
        $source = $this->workingSchool();
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $cycleSection = $this->cycleSection($destination);
        $request = app(RequestCampusMove::class)->request($enrollment, $cycleSection, reason: 'Family moved');

        $this->actAsCampusUser($destination, [CampusMoveAuthority::ApprovePermission]);

        Livewire::test(ListCampusMoveRequests::class)
            ->assertSee($enrollment->user->name)
            ->assertSee($source->name)
            ->assertSee('Family moved')
            ->call('approve', $request->id)
            ->assertHasNoErrors();

        $moved = $enrollment->fresh();

        $this->assertSame(CampusMoveStatus::Approved, $request->fresh()->status);
        $this->assertSame($destination->id, $moved->school_id);
        $this->assertSame($cycleSection->id, $moved->academic_cycle_section_id);
    }

    public function test_the_receiving_campus_can_reject_with_a_note(): void
    {
        $source = $this->workingSchool();
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($destination));

        $this->actAsCampusUser($destination, [CampusMoveAuthority::ApprovePermission]);

        Livewire::test(ListCampusMoveRequests::class)
            ->set("notes.{$request->id}", 'That section is full this year')
            ->call('reject', $request->id)
            ->assertHasNoErrors();

        $decided = $request->fresh();

        $this->assertSame(CampusMoveStatus::Rejected, $decided->status);
        $this->assertSame('That section is full this year', $decided->decision_note);
        $this->assertSame($source->id, $enrollment->fresh()->school_id);
    }

    public function test_the_asking_campus_sees_its_own_request_and_takes_it_back(): void
    {
        $source = $this->workingSchool();
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $source->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($destination));

        $this->authorized_user([CampusMoveAuthority::RequestPermission]);

        Livewire::test(ListCampusMoveRequests::class)
            ->assertSee('Students this campus asked to send away')
            ->assertSee($destination->name)
            ->call('cancel', $request->id)
            ->assertHasNoErrors();

        $this->assertSame(CampusMoveStatus::Cancelled, $request->fresh()->status);
    }

    public function test_the_asking_campus_cannot_approve_its_own_request(): void
    {
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($destination));

        // The permission is held at the asking campus, not the receiving one.
        $this->authorized_user([
            CampusMoveAuthority::RequestPermission,
            CampusMoveAuthority::ApprovePermission,
        ]);

        Livewire::test(ListCampusMoveRequests::class)
            ->call('approve', $request->id)
            ->assertForbidden();

        $this->assertSame(CampusMoveStatus::Requested, $request->fresh()->status);
    }

    public function test_a_stranger_cannot_open_the_screen(): void
    {
        $this->siblingCampus();

        $this->authorized_user(['read student'])
            ->get(route('campus-moves.index'))
            ->assertForbidden();
    }

    public function test_the_receiving_campus_can_open_the_screen(): void
    {
        $destination = $this->siblingCampus();
        // Every dashboard screen needs the campus to have an academic cycle.
        $this->cycleSection($destination);

        $this->actAsCampusUser($destination, [CampusMoveAuthority::ApprovePermission])
            ->get(route('campus-moves.index'))
            ->assertOk()
            ->assertSee('Campus moves');
    }

    public function test_the_screen_only_shows_this_campus_requests(): void
    {
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($destination));

        // A pair of campuses in another organization entirely.
        $otherSource = School::factory()->create();
        $otherDestination = School::factory()->create(['organization_id' => $otherSource->organization_id]);
        $stranger = StudentRecord::factory()->create(['school_id' => $otherSource->id]);
        app(RequestCampusMove::class)->request($stranger, $this->cycleSection($otherDestination));

        $this->actAsCampusUser($destination, [CampusMoveAuthority::ApprovePermission]);

        Livewire::test(ListCampusMoveRequests::class)
            ->assertSee($enrollment->user->name)
            ->assertDontSee($stranger->user->name);
    }

    public function test_a_decided_request_leaves_the_screen(): void
    {
        $destination = $this->siblingCampus();
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        $request = app(RequestCampusMove::class)->request($enrollment, $this->cycleSection($destination));
        app(RequestCampusMove::class)->reject($request);

        $this->actAsCampusUser($destination, [CampusMoveAuthority::ApprovePermission]);

        Livewire::test(ListCampusMoveRequests::class)
            ->assertSee('No campus is waiting on a decision from you');

        $this->assertSame(0, CampusMoveRequest::query()->open()->count());
    }

    /**
     * Sign in as somebody who works in the given campus, and work there.
     *
     * @param  array<int, string>  $permissions
     */
    private function actAsCampusUser(School $school, array $permissions): object
    {
        $user = $this->memberOf($school);

        school_context()->set($school, remember: false);
        $user->givePermissionTo($permissions);

        return $this->actingAsMemberOf($school, $user->refresh());
    }

    /**
     * Make a second campus inside the working school's organization.
     */
    private function siblingCampus(): School
    {
        return School::factory()->create([
            'organization_id' => $this->workingSchool()->organization_id,
        ]);
    }

    private function cycleSection(School $school): AcademicCycleSection
    {
        $academicYear = AcademicYear::query()->where('school_id', $school->id)->first()
            ?? AcademicYear::factory()->create([
                'school_id' => $school->id,
                'start_year' => now()->year,
                'stop_year' => now()->year + 1,
            ]);
        $academicLevel = AcademicLevel::query()->where('school_id', $school->id)->first()
            ?? AcademicLevel::factory()->create(['school_id' => $school->id]);

        // A campus opens its screens only once it has chosen a cycle.
        if ($school->academic_year_id === null) {
            $school->academic_year_id = $academicYear->id;
            $school->save();
        }

        return AcademicCycleSection::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);
    }
}
