<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Livewire\DashboardTrends;
use App\Models\AttendanceRecord;
use App\Models\FeeInvoice;
use App\Models\FeeInvoiceRecord;
use App\Models\Incident;
use App\Models\School;
use App\Models\StudentPayment;
use App\Models\StudentRecord;
use App\Services\Dashboard\SchoolTrends;
use App\Traits\FeatureTestTrait;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTrendsTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private CarbonImmutable $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = CarbonImmutable::parse('2026-09-24');
        $this->travelTo($this->today);
    }

    public function test_attendance_is_rated_by_week_and_leaves_unrecorded_days_out(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => current_school_id()]);

        $this->mark($enrollment, '2026-09-21', AttendanceStatus::Present);
        $this->mark($enrollment, '2026-09-22', AttendanceStatus::Absent);
        $this->mark($enrollment, '2026-09-23', AttendanceStatus::NotRecorded);
        $this->mark($enrollment, '2026-08-11', AttendanceStatus::Late);

        $attendance = app(SchoolTrends::class)->attendanceByWeek($this->today);

        $this->assertCount(8, $attendance['weeks']);
        $this->assertSame(['week' => 'Sep 21', 'rate' => 50.0], $attendance['weeks'][7]);
        $this->assertSame(['week' => 'Aug 10', 'rate' => 100.0], $attendance['weeks'][1]);
        $this->assertNull($attendance['weeks'][0]['rate']);
        $this->assertSame(50.0, $attendance['recent']);
        $this->assertSame(100.0, $attendance['previous']);
    }

    public function test_fees_compare_billed_and_standing_payments_of_this_school_only(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => current_school_id()]);
        $invoice = FeeInvoice::factory()->create([
            'school_id' => current_school_id(),
            'user_id' => $enrollment->user_id,
            'issue_date' => '2026-09-02',
        ]);
        FeeInvoiceRecord::factory()->create(['fee_invoice_id' => $invoice->id, 'amount' => 500, 'fine' => 20, 'waiver' => 70]);

        StudentPayment::factory()->create(['school_id' => current_school_id(), 'student_record_id' => $enrollment->id, 'amount' => 300, 'received_on' => '2026-09-10']);
        $reversed = StudentPayment::factory()->create(['school_id' => current_school_id(), 'student_record_id' => $enrollment->id, 'amount' => 80, 'received_on' => '2026-09-11']);
        StudentPayment::factory()->create(['school_id' => current_school_id(), 'student_record_id' => $enrollment->id, 'amount' => -80, 'received_on' => '2026-09-12', 'reversal_of_id' => $reversed->id]);

        $otherSchool = School::factory()->create();
        StudentPayment::factory()->create(['school_id' => $otherSchool->id, 'amount' => 9000, 'received_on' => '2026-09-10']);

        $fees = app(SchoolTrends::class)->feesByMonth($this->today);

        $this->assertCount(6, $fees);
        $this->assertSame(['month' => 'Sep', 'billed' => 450, 'collected' => 300], $fees[5]);
        $this->assertSame(['month' => 'Apr', 'billed' => 0, 'collected' => 0], $fees[0]);
    }

    public function test_incidents_count_only_the_cases_the_reader_may_see(): void
    {
        $this->authorized_user(['read incident']);
        $viewer = auth()->user();

        Incident::create(['reference' => 'CASE-1', 'summary' => 'Open case', 'occurred_at' => '2026-09-22 10:00:00']);
        Incident::create(['reference' => 'CASE-2', 'summary' => 'Restricted case', 'occurred_at' => '2026-09-22 11:00:00', 'is_restricted' => true]);
        Incident::create(['reference' => 'CASE-3', 'summary' => 'Earlier case', 'occurred_at' => '2026-09-01 09:00:00']);
        Incident::create(['school_id' => School::factory()->create()->id, 'reference' => 'CASE-4', 'summary' => 'Other school', 'occurred_at' => '2026-09-22 09:00:00']);

        $incidents = app(SchoolTrends::class)->incidentsByWeek($viewer, $this->today);

        $this->assertSame(['week' => 'Sep 21', 'incidents' => 1], $incidents[7]);
        $this->assertSame(['week' => 'Aug 31', 'incidents' => 1], $incidents[4]);
        $this->assertSame(2, collect($incidents)->sum('incidents'));
    }

    public function test_enrolment_counts_attending_students_against_section_seats(): void
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => current_school_id()]);
        $section = $enrollment->academicCycleSection;
        $trends = app(SchoolTrends::class);
        $before = collect($trends->enrolmentByClass($section->academic_year_id))->firstWhere('name', $section->academicLevel->name);

        $section->update(['capacity' => $section->capacity + 30]);
        StudentRecord::factory()->create(['school_id' => current_school_id(), 'academic_cycle_section_id' => $section->id]);
        StudentRecord::factory()->create(['school_id' => current_school_id(), 'academic_cycle_section_id' => $section->id, 'status' => EnrollmentStatus::Withdrawn]);

        $after = collect($trends->enrolmentByClass($section->academic_year_id))->firstWhere('name', $section->academicLevel->name);

        $this->assertSame($before['students'] + 1, $after['students']);
        $this->assertSame($before['capacity'] + 30, $after['capacity']);
    }

    public function test_the_trends_section_draws_only_charts_the_person_may_read_and_that_have_data(): void
    {
        $this->authorized_user(['read attendance', 'read incident']);
        $enrollment = StudentRecord::factory()->create(['school_id' => current_school_id()]);
        $this->mark($enrollment, '2026-09-21', AttendanceStatus::Present);

        Livewire::withoutLazyLoading()->test(DashboardTrends::class)
            ->assertSee('id="trends-overview"', false)
            ->assertSee('id="trend-attendance"', false)
            ->assertSee('Weekly attendance rate')
            ->assertDontSee('id="trend-incidents"', false)
            ->assertDontSee('id="trend-fees"', false)
            ->assertDontSee('id="trend-enrolment"', false);
    }

    public function test_the_trends_section_stays_out_when_there_is_nothing_to_draw(): void
    {
        $this->authorized_user(['read attendance', 'read incident', 'read fee invoice']);

        Livewire::withoutLazyLoading()->test(DashboardTrends::class)
            ->assertDontSee('id="trends-overview"', false);
    }

    public function test_the_dashboard_defers_trends_only_for_people_with_a_matching_permission(): void
    {
        $this->authorized_user(['read attendance'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeLivewire(DashboardTrends::class)
            ->assertSee('aria-label="Trends loading"', false);
    }

    public function test_the_dashboard_skips_trends_without_any_matching_permission(): void
    {
        $this->authorized_user(['read notice'])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSeeLivewire(DashboardTrends::class);
    }

    private function mark(StudentRecord $enrollment, string $date, AttendanceStatus $status): void
    {
        AttendanceRecord::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
            'attended_on' => $date,
            'status' => $status,
        ]);
    }
}
