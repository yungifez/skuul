<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Enums\AuditAction;
use App\Livewire\CreateExamForm;
use App\Livewire\EditExamForm;
use App\Livewire\ListExamsTable;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\GradeItem;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_exam_can_be_planned_for_a_draft_calendar_without_setting_it_as_working(): void
    {
        $school = $this->workingSchool();
        $school->forceFill([
            'academic_year_id' => null,
            'academic_period_id' => null,
        ])->save();
        academic_period_context()->forget();

        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);

        $this->authorized_user(['create exam'], $school)
            ->get(route('exams.create', ['academic_year_id' => $academicYear->id]))
            ->assertOk()
            ->assertSee($academicYear->name)
            ->assertSee($academicPeriod->displayName);

        $component = Livewire::withQueryParams(['academic_year_id' => $academicYear->id])
            ->test(CreateExamForm::class)
            ->assertSet('academicPeriodId', (string) $academicPeriod->id)
            ->set('name', 'Draft calendar assessment')
            ->set('startDate', '2026-09-01')
            ->set('stopDate', '2026-09-02')
            ->call('save');

        $exam = Exam::query()->where('name', 'Draft calendar assessment')->firstOrFail();

        $component->assertRedirect(route('academic-years.show', $academicYear));
        $this->assertModelExists($exam);
    }

    public function test_an_exam_can_be_planned_before_the_school_year_opens(): void
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Scheduled,
        ]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Scheduled,
        ]);

        $this->authorized_user(['create exam'], $school);

        Livewire::withQueryParams(['academic_year_id' => $academicYear->id])
            ->test(CreateExamForm::class)
            ->set('academicPeriodId', (string) $academicPeriod->id)
            ->set('name', 'Opening assessment')
            ->set('startDate', '2026-09-01')
            ->set('stopDate', '2026-09-02')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('exams', [
            'name' => 'Opening assessment',
            'academic_period_id' => $academicPeriod->id,
        ]);
    }

    // test unauthorized user cannot view all exams

    public function test_unauthorized_user_cant_view_all_exams()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams')
            ->assertForbidden();
    }

    // test authorized user can view all exams

    public function test_authorized_user_can_view_all_exams()
    {
        $this->authorized_user(['read exam'])
            ->get('/dashboard/exams')
            ->assertOk();
    }

    // test unauthorized user cannot view create exam

    public function test_unauthorized_user_cant_view_create_exam()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/create')
            ->assertForbidden();
    }

    // test authorized user can view create exam

    public function test_user_can_view_create_exam()
    {
        $this->authorized_user(['create exam'])
            ->get('/dashboard/exams/create')
            ->assertOk();
    }

    public function test_unauthorized_user_cant_create_exam()
    {
        $this->unauthorized_user();

        Livewire::test(CreateExamForm::class)->assertForbidden();
    }

    public function test_authorized_user_can_create_exam()
    {
        $period = $this->plannedPeriod();
        $this->authorized_user(['create exam']);

        $this->planExam($period, ['description' => 'test description'])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('exams', [
            'name' => 'test exam',
            'academic_period_id' => $period->id,
            'description' => 'test description',
            'start_date' => '2026-10-05',
            'stop_date' => '2026-10-09',
        ]);
        $this->assertSame(1, AuditEvent::query()->where('action', AuditAction::ExamChanged)->count());
    }

    public function test_an_exam_must_sit_inside_its_reporting_period(): void
    {
        $period = $this->plannedPeriod();
        $this->authorized_user(['create exam']);

        $this->planExam($period, ['startDate' => '2026-08-28', 'stopDate' => '2026-09-02'])
            ->assertHasErrors('startDate');
        $this->planExam($period, ['startDate' => '2026-12-18', 'stopDate' => '2027-01-08'])
            ->assertHasErrors('startDate');

        $this->assertSame(0, Exam::query()->where('academic_period_id', $period->id)->count());
    }

    public function test_an_exam_cannot_end_before_it_starts(): void
    {
        $period = $this->plannedPeriod();
        $this->authorized_user(['create exam']);

        $this->planExam($period, ['startDate' => '2026-10-09', 'stopDate' => '2026-10-05'])
            ->assertHasErrors('stopDate');
    }

    public function test_one_period_never_holds_two_exams_with_one_name(): void
    {
        $period = $this->plannedPeriod();
        $this->authorized_user(['create exam']);

        $this->planExam($period, ['name' => 'Mid-term'])->assertHasNoErrors();
        $this->planExam($period, ['name' => ' MID-TERM '])->assertHasErrors('name');

        $this->assertSame(1, Exam::query()->where('academic_period_id', $period->id)->count());
    }

    public function test_an_exam_cannot_be_planned_in_another_schools_period(): void
    {
        $otherSchool = School::factory()->create();
        $otherYear = AcademicYear::factory()->create(['school_id' => $otherSchool->id, 'status' => AcademicPeriodStatus::Draft]);
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $otherSchool->id,
            'academic_year_id' => $otherYear->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $this->authorized_user(['create exam']);

        $this->planExam($otherPeriod)->assertHasErrors('academicPeriodId');

        $this->assertSame(0, Exam::query()->where('academic_period_id', $otherPeriod->id)->count());
    }

    // test unauthorized user cannot view edit exam

    public function test_unauthorized_user_cant_view_edit_exam()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/edit')
            ->assertForbidden();
    }

    // test authorized user can view edit exam

    public function test_user_can_view_edit_exam()
    {
        $this->authorized_user(['update exam'])
            ->get('/dashboard/exams/1/edit')
            ->assertOk();
    }

    public function test_exam_edit_uses_the_exam_calendar_when_no_working_calendar_is_set(): void
    {
        $school = $this->workingSchool();
        $school->forceFill([
            'academic_year_id' => null,
            'academic_period_id' => null,
        ])->save();
        academic_period_context()->forget();

        $academicYear = AcademicYear::factory()->create([
            'school_id' => $school->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $exam = Exam::factory()->create([
            'academic_period_id' => $academicPeriod->id,
        ]);

        $this->authorized_user(['update exam'], $school)
            ->get(route('exams.edit', $exam))
            ->assertOk()
            ->assertSee($academicPeriod->displayName);
    }

    public function test_unauthorized_user_cant_update_exam()
    {
        $exam = Exam::factory()->create();
        $this->unauthorized_user();

        Livewire::test(EditExamForm::class, ['exam' => $exam])->assertForbidden();
    }

    public function test_authorized_user_can_update_exam()
    {
        $period = $this->plannedPeriod();
        $exam = Exam::factory()->create(['academic_period_id' => $period->id, 'start_date' => '2026-10-05', 'stop_date' => '2026-10-09']);
        $this->authorized_user(['update exam']);

        Livewire::test(EditExamForm::class, ['exam' => $exam])
            ->assertSet('startDate', '2026-10-05')
            ->assertSet('stopDate', '2026-10-09')
            ->set('name', 'test')
            ->set('description', 'test')
            ->set('stopDate', '2026-10-12')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('academic-years.show', $period->academic_year_id));

        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'name' => 'test',
            'academic_period_id' => $period->id,
            'description' => 'test',
            'start_date' => '2026-10-05',
            'stop_date' => '2026-10-12',
        ]);
    }

    public function test_an_exam_with_marked_papers_stays_in_its_period(): void
    {
        $period = $this->plannedPeriod();
        $otherPeriod = AcademicPeriod::factory()->create([
            'school_id' => $period->school_id,
            'academic_year_id' => $period->academic_year_id,
            'status' => AcademicPeriodStatus::Draft,
        ]);
        $exam = Exam::factory()->create(['academic_period_id' => $period->id, 'start_date' => '2026-10-05', 'stop_date' => '2026-10-09']);
        $slot = ExamSlot::factory()->create(['exam_id' => $exam->id]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $period->school_id,
            'academic_year_id' => $period->academic_year_id,
            'academic_period_id' => $period->id,
        ]);
        GradeItem::create([
            'school_id' => $period->school_id,
            'course_offering_id' => $courseOffering->id,
            'name' => 'Mid-term paper',
        ])->forceFill(['exam_slot_id' => $slot->id])->save();
        $this->authorized_user(['update exam']);

        Livewire::test(EditExamForm::class, ['exam' => $exam])
            ->set('academicPeriodId', (string) $otherPeriod->id)
            ->call('save')
            ->assertHasErrors('academicPeriodId');

        $this->assertSame($period->id, $exam->fresh()->academic_period_id);
    }

    // test unauthorized user cannot view exam

    public function test_unauthorized_user_cannot_view_exam()
    {
        $exam = Exam::factory()->create();
        $this->unauthorized_user()
            ->get("dashboard/exams/$exam->id/edit")
            ->assertForbidden();
    }

    // test unauthorized user cannot view exam

    public function test_authorized_user_can_view_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['read exam'])
            ->get("dashboard/exams/$exam->id/edit")
            ->assertForbidden();
    }

    public function test_authorized_user_can_open_an_exam_and_manage_its_slots(): void
    {
        $exam = Exam::factory()->create();

        $this->authorized_user(['read exam'])
            ->get(route('exams.show', $exam))
            ->assertOk()
            ->assertSee('Exam Slots In '.$exam->name);
    }

    // test unauthorized user cannot view exam

    public function test_unauthorized_user_cannot_delete_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['read exam']);

        Livewire::test(ListExamsTable::class)
            ->call('deleteExam', $exam->id)
            ->assertForbidden();

        $this->assertModelExists($exam);
    }

    public function test_authorized_user_can_delete_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['read exam', 'delete exam']);

        Livewire::test(ListExamsTable::class)
            ->call('deleteExam', $exam->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelMissing($exam);
    }

    public function test_another_schools_exam_cannot_be_deleted()
    {
        $otherSchool = School::factory()->create();
        $theirs = Exam::factory()->create([
            'academic_period_id' => AcademicPeriod::factory()->create(['school_id' => $otherSchool->id])->id,
        ]);
        $this->authorized_user(['read exam', 'delete exam']);

        try {
            Livewire::test(ListExamsTable::class)->call('deleteExam', $theirs->id);
            $this->fail('Another school\'s exam was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($theirs);
    }

    public function test_legacy_exam_result_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('exam-records.index'));
        $this->assertFalse(Route::has('exams.tabulation'));
        $this->assertFalse(Route::has('exams.academic-period-result-tabulation'));
        $this->assertFalse(Route::has('exams.academic-year-result-tabulation'));
        $this->assertFalse(Route::has('exams.result-checker'));
        $this->assertFalse(Route::has('exams.set-publish-result-status'));
    }

    /**
     * Make a draft period that runs from September to December 2026.
     */
    private function plannedPeriod(): AcademicPeriod
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::factory()->create(['school_id' => $school->id, 'status' => AcademicPeriodStatus::Draft]);

        return AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'status' => AcademicPeriodStatus::Draft,
            'starts_on' => '2026-09-01',
            'ends_on' => '2026-12-18',
        ]);
    }

    /**
     * Plan an exam in a period through the form.
     *
     * @param  array<string, string>  $values
     */
    private function planExam(AcademicPeriod $period, array $values = []): Testable
    {
        $component = Livewire::withQueryParams(['academic_year_id' => $period->academic_year_id])
            ->test(CreateExamForm::class)
            ->set('academicPeriodId', (string) $period->id);

        foreach ($values + ['name' => 'test exam', 'startDate' => '2026-10-05', 'stopDate' => '2026-10-09'] as $property => $value) {
            $component->set($property, $value);
        }

        return $component->call('save');
    }
}
