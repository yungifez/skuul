<?php

namespace Tests\Feature;

use App\Enums\BoardingRollEntryStatus;
use App\Enums\CalendarEventType;
use App\Enums\Feature;
use App\Enums\IncidentStatus;
use App\Enums\LeaveStatus;
use App\Enums\NoticeStatus;
use App\Enums\ParticipationStatus;
use App\Enums\SupportPlanStatus;
use App\Models\BoardingResidence;
use App\Models\BoardingRoll;
use App\Models\CalendarEvent;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Incident;
use App\Models\LibraryCopy;
use App\Models\LibraryLoan;
use App\Models\Notice;
use App\Models\Program;
use App\Models\StaffLeaveRequest;
use App\Models\SupportPlan;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The demo fills the screens of school life with readable, current records.
 */
class DemoStudentLifeTest extends TestCase
{
    use RefreshDatabase;

    private DemoSchool $demo;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new DemoSchoolSeeder;
        Model::unguarded(fn () => $seeder->run());
        $this->demo = $seeder->demoSchool();
    }

    public function test_the_calendar_and_notice_board_show_the_coming_days(): void
    {
        $upcoming = CalendarEvent::query()
            ->where('school_id', $this->demo->campus->id)
            ->published()
            ->between(now()->addDay(), now()->addDays(7))
            ->pluck('title');

        $this->assertContains('Fall open house', $upcoming);
        $this->assertContains('Homecoming game', $upcoming);
        $this->assertTrue(CalendarEvent::query()->published()->where('type', CalendarEventType::Closure)->where('starts_at', '>', now())->exists());
        $this->assertTrue(CalendarEvent::query()->published()->where('title', 'Thanksgiving break')->exists());

        $notices = Notice::query()->where('school_id', $this->demo->campus->id)->get()->keyBy('title');

        $this->assertSame(NoticeStatus::Published, $notices['Picture day is Thursday']->status);
        $this->assertSame(NoticeStatus::Scheduled, $notices['Winter concert tickets']->status);
        $this->assertTrue($notices['Picture day is Thursday']->recipients()->where('user_id', $this->demo->demoParent->id)->exists());
        $this->assertSame(2, Notice::query()->where('school_id', $this->demo->campus->id)->published()->active()->count());
    }

    public function test_care_and_conduct_records_show_cases_plans_and_leave(): void
    {
        $statuses = Incident::query()->where('school_id', $this->demo->campus->id)->pluck('status', 'summary');

        $this->assertSame(IncidentStatus::Resolved, $statuses['Phone use during a quiz']);
        $this->assertSame(IncidentStatus::UnderReview, $statuses['Disagreement in the cafeteria']);

        $plan = SupportPlan::query()->where('title', 'Extended time on tests')->withCount('actions')->sole();

        $this->assertSame(SupportPlanStatus::Active, $plan->status);
        $this->assertNotNull($plan->review_on);
        $this->assertSame(3, $plan->actions_count);
        $this->assertSame(2, $plan->actions()->whereNotNull('completed_at')->count());

        $this->assertEqualsCanonicalizing(
            [LeaveStatus::Approved, LeaveStatus::Declined, LeaveStatus::Requested],
            StaffLeaveRequest::query()->where('school_id', $this->demo->campus->id)->get()->pluck('status')->all(),
        );
    }

    public function test_clubs_rooms_boarding_and_library_hold_current_records(): void
    {
        $robotics = Program::query()->where('name', 'Robotics club')->sole();
        $states = $robotics->participations()->pluck('status')->unique();

        $this->assertTrue($states->contains(ParticipationStatus::Active));
        $this->assertTrue($states->contains(ParticipationStatus::Requested));
        $this->assertTrue($states->contains(ParticipationStatus::Withdrawn));

        $this->assertFalse(Facility::query()->where('name', 'Activity bus')->sole()->is_active);
        $this->assertTrue(FacilityBooking::query()->whereHas('facility', fn ($facility) => $facility->where('name', 'Gymnasium'))->where('starts_at', '>', now())->exists());

        $this->assertTrue(features()->enabled(Feature::Boarding, $this->demo->campus));
        $this->assertSame('Riverside Residence Hall', BoardingResidence::query()->sole()->name);
        $completed = BoardingRoll::query()->whereNotNull('completed_at')->sole();
        $this->assertSame(5, $completed->entries()->count());
        $this->assertTrue($completed->entries()->where('status', BoardingRollEntryStatus::Late)->exists());
        $this->assertTrue(BoardingRoll::query()->whereNull('completed_at')->sole()->entries()->where('status', BoardingRollEntryStatus::NotRecorded)->exists());

        $this->assertTrue(features()->enabled(Feature::Library, $this->demo->campus));
        $this->assertSame(12, LibraryCopy::query()->where('school_id', $this->demo->campus->id)->count());
        $open = LibraryLoan::query()->where('school_id', $this->demo->campus->id)->whereNull('returned_on')->get();
        $this->assertCount(5, $open);
        $this->assertSame(2, $open->filter(fn (LibraryLoan $loan): bool => $loan->due_on->lt(today()))->count());
        $this->assertTrue($open->contains('user_id', $this->demo->demoStudent->id));
    }
}
