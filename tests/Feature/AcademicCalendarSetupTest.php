<?php

namespace Tests\Feature;

use App\Actions\Academic\SaveAcademicCalendar;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Exceptions\InvalidValueException;
use App\Livewire\AcademicCalendarForm;
use App\Livewire\ShowAcademicYear;
use App\Models\AcademicYear;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class AcademicCalendarSetupTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_school_can_create_a_three_term_draft_calendar_from_dates(): void
    {
        $this->authorized_user(['create academic year']);

        Livewire::test(AcademicCalendarForm::class)
            ->set('startsOn', '2030-09-01')
            ->set('endsOn', '2031-08-31')
            ->set('structure', 'three_terms')
            ->call('generatePeriods')
            ->call('save');

        $calendar = AcademicYear::inSchool()->whereDate('starts_on', '2030-09-01')->firstOrFail();

        $this->assertSame('2030', $calendar->start_year);
        $this->assertSame('2031', $calendar->stop_year);
        $this->assertSame('2030–31', $calendar->name);
        $this->assertSame(AcademicPeriodStatus::Draft, $calendar->status);
        $this->assertSame(['Term 1', 'Term 2', 'Term 3'], $calendar->topLevelPeriods()->pluck('name')->all());
        $this->assertSame('2031-08-31', $calendar->topLevelPeriods()->get()->last()->ends_on->toDateString());
    }

    public function test_calendar_creation_rejects_overlapping_reporting_periods(): void
    {
        $this->expectException(InvalidValueException::class);

        app(SaveAcademicCalendar::class)->save(
            $this->workingSchool(),
            Carbon::parse('2030-09-01'),
            Carbon::parse('2031-08-31'),
            [
                ['name' => 'Term 1', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2030-09-01', 'ends_on' => '2031-01-31'],
                ['name' => 'Term 2', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2031-01-31', 'ends_on' => '2031-06-30'],
            ],
        );
    }

    public function test_publishing_a_future_calendar_schedules_its_reporting_periods(): void
    {
        $this->authorized_user(['create academic year', 'update academic year']);
        $actor = auth()->user();
        $calendar = app(SaveAcademicCalendar::class)->save(
            $this->workingSchool(),
            Carbon::parse('2030-09-01'),
            Carbon::parse('2031-08-31'),
            [
                ['name' => 'Term 1', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2030-09-01', 'ends_on' => '2030-12-20'],
                ['name' => 'Term 2', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2031-01-06', 'ends_on' => '2031-03-28'],
                ['name' => 'Term 3', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2031-04-14', 'ends_on' => '2031-08-31'],
            ],
            auth()->user(),
        );

        Livewire::test(ShowAcademicYear::class, ['academicYear' => $calendar])
            ->call('publishCalendar')
            ->assertHasNoErrors();

        $calendar = $calendar->fresh();

        $this->assertSame(AcademicPeriodStatus::Scheduled, $calendar->status);
        $this->assertCount(3, $calendar->topLevelPeriods()->where('status', AcademicPeriodStatus::Scheduled)->get());
        $this->assertSame($actor?->id, $calendar->statusChanges()->firstOrFail()->changed_by);
    }

    public function test_a_draft_calendar_cannot_be_made_the_working_calendar(): void
    {
        $calendar = app(SaveAcademicCalendar::class)->save(
            $this->workingSchool(),
            Carbon::parse('2030-09-01'),
            Carbon::parse('2031-08-31'),
            [['name' => 'Term 1', 'type' => AcademicPeriodType::Term->value, 'starts_on' => '2030-09-01', 'ends_on' => '2031-08-31']],
        );

        $this->authorized_user(['set academic year'])
            ->post(route('academic-years.set-academic-year'), ['academic_year_id' => $calendar->id])
            ->assertSessionHas('danger', 'Publish the school calendar before making it the working calendar.');
    }

    public function test_the_school_calendar_setup_screen_replaces_the_legacy_year_fields(): void
    {
        $this->authorized_user(['create academic year'])
            ->get(route('academic-years.create'))
            ->assertOk()
            ->assertSee('Set up a school calendar')
            ->assertSee('Reporting structure')
            ->assertDontSee('Stop year');
    }
}
