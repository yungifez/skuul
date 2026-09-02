<?php

namespace Tests\Feature;

use App\Actions\Academic\SaveAcademicCalendar;
use App\Enums\AcademicPeriodStatus;
use App\Enums\AcademicPeriodType;
use App\Enums\AuditAction;
use App\Enums\InstructionalModel;
use App\Exceptions\InvalidValueException;
use App\Livewire\AcademicCalendarForm;
use App\Livewire\ShowAcademicYear;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\InstructionalModelSetting;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
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

    public function test_editing_term_dates_preserves_timetables_attached_to_that_term(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['update academic year'], $school);
        $calendar = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
            'starts_on' => '2030-09-01',
            'ends_on' => '2031-08-31',
        ]);
        $period = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $calendar->id,
            'name' => 'Term 1',
            'position' => 1,
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $timetable = Timetable::create([
            'name' => 'Term 1 timetable',
            'academic_period_id' => $period->id,
            'academic_cycle_section_id' => null,
        ]);

        app(SaveAcademicCalendar::class)->save(
            $school,
            Carbon::parse('2030-09-01'),
            Carbon::parse('2031-08-31'),
            [[
                'id' => $period->id,
                'name' => 'Term 1',
                'type' => AcademicPeriodType::Term->value,
                'starts_on' => '2030-09-08',
                'ends_on' => '2030-12-20',
            ]],
            auth()->user(),
            $calendar,
        );

        $this->assertModelExists($timetable);
        $this->assertSame($period->id, $timetable->fresh()->academic_period_id);
        $this->assertSame('2030-09-08', $period->fresh()->starts_on->toDateString());
    }

    public function test_removing_a_term_with_timetables_is_refused(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['update academic year'], $school);
        $calendar = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
            'starts_on' => '2030-09-01',
            'ends_on' => '2031-08-31',
        ]);
        $termOne = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $calendar->id,
            'name' => 'Term 1',
            'position' => 1,
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $termTwo = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $calendar->id,
            'name' => 'Term 2',
            'position' => 2,
            'starts_on' => '2031-01-06',
            'ends_on' => '2031-04-04',
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $timetable = Timetable::create([
            'name' => 'Term 2 timetable',
            'academic_period_id' => $termTwo->id,
            'academic_cycle_section_id' => null,
        ]);
        $this->assertSame($termTwo->id, $timetable->fresh()->academic_period_id);
        $this->assertSame(1, Timetable::query()->where('academic_period_id', $termTwo->id)->count());

        try {
            app(SaveAcademicCalendar::class)->save(
                $school,
                Carbon::parse('2030-09-01'),
                Carbon::parse('2031-08-31'),
                [[
                    'id' => $termOne->id,
                    'name' => 'Term 1',
                    'type' => AcademicPeriodType::Term->value,
                    'starts_on' => '2030-09-01',
                    'ends_on' => '2030-12-20',
                ]],
                auth()->user(),
                $calendar,
            );
            $this->fail('A term with attached timetables should not be removable.');
        } catch (InvalidValueException $exception) {
            $this->assertStringContainsString('Term 2 has 1 timetable', $exception->getMessage());
        }

        $this->assertModelExists($termTwo);
        $this->assertModelExists($timetable);
    }

    public function test_date_changes_warn_about_one_date_timetables_before_saving(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['update academic year'], $school);
        $calendar = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
            'starts_on' => '2030-09-01',
            'ends_on' => '2031-08-31',
        ]);
        $period = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $calendar->id,
            'name' => 'Term 1',
            'position' => 1,
            'starts_on' => '2030-09-01',
            'ends_on' => '2030-12-20',
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $timetable = Timetable::create([
            'name' => 'Sports day',
            'academic_period_id' => $period->id,
            'academic_cycle_section_id' => null,
        ]);
        $slot = TimetableTimeSlot::create([
            'timetable_id' => $timetable->id,
            'start_time' => '10:00',
            'stop_time' => '11:00',
            'recurrence' => 'one_time',
            'occurs_on' => '2030-09-05',
        ]);

        $form = Livewire::test(AcademicCalendarForm::class, ['academicYear' => $calendar])
            ->set('periods.0.starts_on', '2030-09-08')
            ->call('save')
            ->assertSet('showDateImpactWarning', true)
            ->assertCount('dateImpactWarnings', 1);

        $this->assertSame('2030-09-01', $period->fresh()->starts_on->toDateString());

        $form->call('saveWithDateImpact');

        $this->assertSame('2030-09-08', $period->fresh()->starts_on->toDateString());
        $this->assertTrue($slot->fresh()->occursOutsideAcademicPeriod());
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

    public function test_setup_rollover_shows_a_preview_before_creating_new_year_setup(): void
    {
        $school = $this->workingSchool();
        $source = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2025,
            'stop_year' => 2026,
        ]);
        $target = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2026,
            'stop_year' => 2027,
        ]);
        AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $source->id,
            'name' => 'Term 1',
            'position' => 1,
            'starts_on' => '2025-09-01',
            'ends_on' => '2025-12-19',
            'status' => AcademicPeriodStatus::Open,
        ]);
        AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $source->id,
            'name' => 'Term 2',
            'position' => 2,
            'starts_on' => '2026-01-05',
            'ends_on' => '2026-06-30',
            'status' => AcademicPeriodStatus::Open,
        ]);
        InstructionalModelSetting::create([
            'school_id' => $school->id,
            'academic_year_id' => $source->id,
            'model' => InstructionalModel::Hybrid,
        ]);

        $this->authorized_user(['update academic year'], $school);

        Livewire::test(ShowAcademicYear::class, ['academicYear' => $target])
            ->call('openSetupRolloverDialog')
            ->assertSet('showSetupRolloverDialog', true)
            ->assertSet('setupRolloverPreview.create_count', 3)
            ->assertSee('One class group, with exceptions')
            ->assertSee('Term 1')
            ->assertSee('Nothing is created until you confirm');

        $this->assertDatabaseMissing('instructional_model_settings', [
            'academic_year_id' => $target->id,
        ]);
        $this->assertSame(0, AcademicPeriod::query()->where('academic_year_id', $target->id)->count());

        Livewire::test(ShowAcademicYear::class, ['academicYear' => $target])
            ->call('rollForwardSetup');

        $this->assertDatabaseHas('instructional_model_settings', [
            'academic_year_id' => $target->id,
            'model' => InstructionalModel::Hybrid->value,
        ]);
        $this->assertSame(2, AcademicPeriod::query()->where('academic_year_id', $target->id)->count());
        $this->assertDatabaseHas('academic_periods', [
            'academic_year_id' => $target->id,
            'name' => 'Term 1',
            'starts_on' => '2026-09-01',
            'ends_on' => '2026-12-19',
        ]);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AcademicYearSetupRolledForward)->first());
    }

    public function test_academic_year_screen_explains_lifecycle_and_links_exam_creation_to_that_year(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'start_year' => 2026,
            'stop_year' => 2027,
            'status' => AcademicPeriodStatus::Open,
        ]);
        $period = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Open,
        ]);

        $this->authorized_user(['read academic year', 'create exam'], $school)
            ->get(route('academic-years.show', $academicYear))
            ->assertOk()
            ->assertSee('How a school year moves')
            ->assertSee('Finish existing work and resolve the closing checks.')
            ->assertSee('Add exam')
            ->assertSee('academic_year_id='.$academicYear->id, false);

        $this->get(route('exams.create', ['academic_year_id' => $academicYear->id]))
            ->assertOk()
            ->assertSee('Create an exam for '.$academicYear->name)
            ->assertSee('Exam setup help')
            ->assertSee('value="'.$period->id.'" selected', false);
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
            ->assertSee('Set up a '.strtolower(school_term('academic_year', 'school year')))
            ->assertSee('Reporting structure')
            ->assertDontSee('Stop year');
    }
}
