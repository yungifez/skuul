<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\Semester\SemesterService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An academic year holds ordered periods with a kind and dates.
 */
class AcademicCalendarTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_new_period_is_a_semester_in_the_next_place(): void
    {
        $this->authorized_user(['create semester']);
        $year = current_academic_year();
        $existing = $year->semesters()->count();

        $period = app(SemesterService::class)->createSemester(['name' => 'Third term']);

        $this->assertSame(AcademicPeriodType::Semester, $period->type);
        $this->assertSame($existing + 1, $period->position);
    }

    public function test_a_school_can_use_terms_instead_of_semesters(): void
    {
        $this->authorized_user(['create semester']);

        $period = app(SemesterService::class)->createSemester([
            'name' => 'First term',
            'type' => AcademicPeriodType::Term->value,
        ]);

        $this->assertSame(AcademicPeriodType::Term, $period->type);
        $this->assertSame('Term', $period->typeLabel);
    }

    public function test_periods_are_read_in_teaching_order(): void
    {
        $this->authorized_user(['create semester']);
        $year = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);

        Semester::factory()->create(['academic_year_id' => $year->id, 'name' => 'Second', 'position' => 2]);
        Semester::factory()->create(['academic_year_id' => $year->id, 'name' => 'First', 'position' => 1]);

        $this->assertSame(['First', 'Second'], $year->semesters()->pluck('name')->all());
    }

    public function test_a_period_with_dates_says_which_day_it_covers(): void
    {
        $period = Semester::factory()->create([
            'starts_on' => '2026-09-01',
            'ends_on'   => '2026-12-18',
        ]);

        $this->assertTrue($period->covers('2026-09-01'));
        $this->assertTrue($period->covers('2026-10-15'));
        $this->assertTrue($period->covers('2026-12-18'));
        $this->assertFalse($period->covers('2026-12-19'));
        $this->assertFalse($period->covers('2026-08-31'));
    }

    public function test_a_period_without_dates_covers_nothing(): void
    {
        $this->assertFalse(Semester::factory()->create()->covers('2026-10-15'));
    }

    public function test_the_year_finds_the_period_that_covers_a_day(): void
    {
        $year = AcademicYear::factory()->create(['school_id' => $this->workingSchool()->id]);
        $first = Semester::factory()->create([
            'academic_year_id' => $year->id,
            'position'         => 1,
            'starts_on'        => '2026-09-01',
            'ends_on'          => '2026-12-18',
        ]);
        Semester::factory()->create([
            'academic_year_id' => $year->id,
            'position'         => 2,
            'starts_on'        => '2027-01-06',
            'ends_on'          => '2027-04-02',
        ]);

        $this->assertSame($first->id, $year->periodForDate('2026-10-15')?->id);
        $this->assertNull($year->periodForDate('2026-12-20'));
    }

    public function test_two_periods_cannot_share_a_day(): void
    {
        $this->authorized_user(['create semester']);
        $service = app(SemesterService::class);
        $service->createSemester(['name' => 'First term', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-18']);

        $this->expectException(InvalidValueException::class);

        $service->createSemester(['name' => 'Second term', 'starts_on' => '2026-12-01', 'ends_on' => '2027-03-31']);
    }

    public function test_a_period_cannot_end_before_it_starts(): void
    {
        $this->authorized_user(['create semester']);

        $this->expectException(InvalidValueException::class);

        app(SemesterService::class)->createSemester([
            'name'      => 'Backwards term',
            'starts_on' => '2026-12-18',
            'ends_on'   => '2026-09-01',
        ]);
    }

    public function test_a_period_needs_both_dates_or_neither(): void
    {
        $this->authorized_user(['create semester']);

        $this->expectException(InvalidValueException::class);

        app(SemesterService::class)->createSemester(['name' => 'Half a term', 'starts_on' => '2026-09-01']);
    }

    public function test_a_period_can_be_moved_without_touching_its_neighbours(): void
    {
        $this->authorized_user(['update semester']);
        $service = app(SemesterService::class);
        $period = $service->createSemester(['name' => 'First term', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-18']);

        $service->updateSemester($period, [
            'name'      => 'First term',
            'starts_on' => '2026-09-07',
            'ends_on'   => '2026-12-20',
        ]);

        $period = $period->fresh();

        $this->assertSame('2026-09-07', $period->starts_on->toDateString());
        $this->assertSame('2026-12-20', $period->ends_on->toDateString());
    }

    public function test_the_form_refuses_an_end_date_before_the_start(): void
    {
        $this->authorized_user(['create semester'])
            ->post('/dashboard/semesters', [
                'name'      => 'Backwards term',
                'starts_on' => '2026-12-18',
                'ends_on'   => '2026-09-01',
            ])
            ->assertSessionHasErrors('ends_on');
    }

    public function test_the_form_refuses_an_unknown_period_type(): void
    {
        $this->authorized_user(['create semester'])
            ->post('/dashboard/semesters', ['name' => 'Odd term', 'type' => 'fortnight'])
            ->assertSessionHasErrors('type');
    }
}
