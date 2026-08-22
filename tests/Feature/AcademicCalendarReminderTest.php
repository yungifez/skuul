<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CalendarTemplate;
use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Notifications\AcademicCalendarReminder;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AcademicCalendarReminderTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_reminds_staff_before_a_scheduled_period_starts(): void
    {
        Carbon::setTestNow('2030-08-18 08:00:00');

        $organization = Organization::factory()->create();
        $school = School::factory()->create(['organization_id' => $organization->id]);
        $template = CalendarTemplate::factory()->create([
            'organization_id'    => $organization->id,
            'remind_days_before' => 14,
        ]);
        $school->calendar_template_id = $template->id;
        $school->save();
        $year = AcademicYear::factory()->create([
            'school_id'  => $school->id,
            'start_year' => 2030,
            'stop_year'  => 2031,
        ]);
        AcademicPeriod::factory()->create([
            'school_id'        => $school->id,
            'academic_year_id' => $year->id,
            'name'             => 'Term 1',
            'status'           => AcademicPeriodStatus::Scheduled,
            'starts_on'        => '2030-09-01',
            'ends_on'          => '2030-11-23',
        ]);
        $staffMember = User::factory()->create();
        $this->memberOf($school, $staffMember);
        school_context()->set($school, remember: false);
        $staffMember->givePermissionTo('close academic period');

        Notification::fake();

        $this->artisan('skuul:send-academic-calendar-reminders')
            ->expectsOutputToContain('1 reminder(s) sent.')
            ->assertSuccessful();

        Notification::assertSentTo($staffMember, AcademicCalendarReminder::class);
    }
}
