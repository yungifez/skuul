<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\NoticeStatus;
use App\Livewire\DashboardDataCards;
use App\Models\AcademicPeriod;
use App\Models\Notice;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_dashboard_lists_only_notices_running_today(): void
    {
        $this->authorized_user(['read notice']);
        $schoolId = current_school_id();

        Notice::factory()->create([
            'school_id' => $schoolId,
            'title' => 'Sports day moves to Friday',
            'status' => NoticeStatus::Published,
            'active' => true,
            'start_date' => now()->subDay()->toDateString(),
            'stop_date' => now()->addDays(3)->toDateString(),
        ]);
        Notice::factory()->create([
            'school_id' => $schoolId,
            'title' => 'Last term fees reminder',
            'status' => NoticeStatus::Published,
            'active' => true,
            'start_date' => now()->subMonth()->toDateString(),
            'stop_date' => now()->subWeek()->toDateString(),
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="notices-overview"', false)
            ->assertSee('Sports day moves to Friday')
            ->assertSee('Until '.now()->addDays(3)->format('M j'))
            ->assertDontSee('Last term fees reminder')
            ->assertDontSee('data-slot="data-table"', false);
    }

    public function test_the_dashboard_sends_no_platform_wide_counts_to_the_browser(): void
    {
        $this->authorized_user(['read student']);

        $data = Livewire::test(DashboardDataCards::class)->snapshot['data'];

        $this->assertArrayNotHasKey('schools', $data);
        $this->assertArrayNotHasKey('organizations', $data);
    }

    public function test_the_open_period_count_leaves_out_periods_that_are_not_open(): void
    {
        $this->authorized_user(['read academic period']);
        $academicYear = current_academic_year();
        $this->assertNotNull($academicYear);
        $before = Livewire::test(DashboardDataCards::class)->get('academicPeriods');

        foreach ([AcademicPeriodStatus::Draft, AcademicPeriodStatus::Closed] as $status) {
            AcademicPeriod::factory()->create(['school_id' => current_school_id(), 'academic_year_id' => $academicYear->id, 'status' => $status]);
        }

        Livewire::test(DashboardDataCards::class)->assertSet('academicPeriods', $before);

        AcademicPeriod::factory()->create(['school_id' => current_school_id(), 'academic_year_id' => $academicYear->id, 'status' => AcademicPeriodStatus::Open]);

        Livewire::test(DashboardDataCards::class)->assertSet('academicPeriods', $before + 1);
    }

    public function test_the_dashboard_hides_notices_from_people_who_cannot_read_them(): void
    {
        $this->authorized_user(['read student'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('id="notices-overview"', false);
    }

    public function test_attendance_without_a_register_shows_one_quiet_line_and_no_empty_chart(): void
    {
        $this->authorized_user(['read attendance'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="today-overview"', false)
            ->assertSee('No register taken yet.')
            ->assertDontSee('Attendance rate over the last seven days');
    }

    public function test_the_dashboard_no_longer_carries_a_working_year_card(): void
    {
        $this->authorized_user(['set academic year'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Set working '.strtolower(school_term('academic_year', 'school year')));
    }
}
