<?php

namespace Tests\Feature;

use App\Livewire\CreateExamSlotForm;
use App\Livewire\EditExamSlotForm;
use App\Livewire\ListExamSlotsTable;
use App\Models\AcademicPeriod;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\School;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExamSlotTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    // test unauthorized user can not see all exam slots

    public function test_unauthorized_user_can_not_see_all_exam_slots()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/manage/exam-slots')
            ->assertForbidden();
    }

    // test authorized user can see all exam slots

    public function test_authorized_user_can_see_all_exam_slots()
    {
        $this->authorized_user(['read exam slot'])
            ->get('/dashboard/exams/1/manage/exam-slots')
            ->assertSuccessful();
    }

    // test unauthorized user cannot view create exam slot

    public function test_unauthorized_user_cant_view_create_exam_slot()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/manage/exam-slots/create')
            ->assertForbidden();
    }
    // test authorized user can view create exam slot

    public function test_user_can_view_create_exam_slot()
    {
        $this->authorized_user(['create exam slot'])
            ->get('/dashboard/exams/1/manage/exam-slots/create')
            ->assertOk();
    }

    public function test_unauthorized_user_cant_create_exam_slot()
    {
        $this->unauthorized_user();

        Livewire::test(CreateExamSlotForm::class, ['exam' => Exam::findOrFail(1)])->assertForbidden();
    }

    public function test_authorized_user_can_create_exam_slot()
    {
        $exam = Exam::findOrFail(1);
        $this->authorized_user(['create exam slot']);

        Livewire::test(CreateExamSlotForm::class, ['exam' => $exam])
            ->set('name', 'test exam slot')
            ->set('description', 'test description')
            ->set('totalMarks', '20')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('exam-slots.index', $exam));

        $this->assertDatabaseHas('exam_slots', [
            'exam_id' => $exam->id,
            'name' => 'test exam slot',
            'description' => 'test description',
            'total_marks' => 20,
        ]);
    }

    public function test_one_exam_never_holds_two_papers_with_one_name(): void
    {
        $exam = Exam::findOrFail(1);
        $exam->examSlots()->create(['name' => 'Mathematics paper 1', 'total_marks' => 100]);
        $this->authorized_user(['create exam slot']);

        Livewire::test(CreateExamSlotForm::class, ['exam' => $exam])
            ->set('name', ' mathematics PAPER 1 ')
            ->call('save')
            ->assertHasErrors('name');

        $this->assertSame(1, $exam->examSlots()->where('name', 'like', 'mathematics paper 1')->count());
    }

    public function test_a_paper_needs_a_highest_mark_between_one_and_a_thousand(): void
    {
        $exam = Exam::findOrFail(1);
        $this->authorized_user(['create exam slot']);

        foreach (['0', '-5', '1001', '12.5', ''] as $totalMarks) {
            Livewire::test(CreateExamSlotForm::class, ['exam' => $exam])
                ->set('name', 'Paper')
                ->set('totalMarks', $totalMarks)
                ->call('save')
                ->assertHasErrors('totalMarks');
        }

        $this->assertSame(0, $exam->examSlots()->where('name', 'Paper')->count());
    }

    // test unauthorized user cannot view edit exam slot

    public function test_unauthorized_user_cant_view_edit_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->unauthorized_user()
            ->get("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id/edit")
            ->assertForbidden();
    }

    // test authorized user can view edit exam slot

    public function test_authorized_user_can_view_edit_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['update exam slot'])
            ->get("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id/edit")
            ->assertSuccessful();
    }

    public function test_unauthorized_user_cant_update_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->unauthorized_user();

        Livewire::test(EditExamSlotForm::class, ['exam' => $examSlot->exam, 'examSlot' => $examSlot])->assertForbidden();
    }

    public function test_authorized_user_can_update_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['update exam slot']);

        Livewire::test(EditExamSlotForm::class, ['exam' => $examSlot->exam, 'examSlot' => $examSlot])
            ->assertSet('totalMarks', (string) $examSlot->total_marks)
            ->set('name', 'test exam slot')
            ->set('description', 'test description')
            ->set('totalMarks', '10')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('exam_slots', [
            'id' => $examSlot->id,
            'name' => 'test exam slot',
            'description' => 'test description',
            'total_marks' => 10,
        ]);
    }

    public function test_a_paper_is_only_opened_under_its_own_exam(): void
    {
        $examSlot = ExamSlot::factory()->create();
        $otherExam = Exam::factory()->create(['academic_period_id' => $examSlot->exam->academic_period_id]);

        $this->authorized_user(['update exam slot', 'delete exam slot'])
            ->get(route('exam-slots.edit', [$otherExam, $examSlot]))
            ->assertNotFound();

        try {
            Livewire::test(ListExamSlotsTable::class, ['exam' => $otherExam])->call('deleteSlot', $examSlot->id);
            $this->fail('A slot of another exam was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($examSlot);
    }

    // test unauthorized user cannot delete exam slot

    public function test_unauthorized_user_cant_delete_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['read exam slot']);

        Livewire::test(ListExamSlotsTable::class, ['exam' => $examSlot->exam])
            ->call('deleteSlot', $examSlot->id)
            ->assertForbidden();

        $this->assertModelExists($examSlot);
    }

    // test authorized user can delete exam slot

    public function test_authorized_user_can_delete_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['read exam slot', 'delete exam slot']);

        Livewire::test(ListExamSlotsTable::class, ['exam' => $examSlot->exam])
            ->call('deleteSlot', $examSlot->id)
            ->assertDispatched('status-message', type: 'success');

        $this->assertModelMissing($examSlot);
    }

    public function test_another_schools_exam_slot_cannot_be_deleted()
    {
        $otherSchool = School::factory()->create();
        $theirExam = Exam::factory()->create([
            'academic_period_id' => AcademicPeriod::factory()->create(['school_id' => $otherSchool->id])->id,
        ]);
        $theirs = ExamSlot::factory()->create(['exam_id' => $theirExam->id]);
        $this->authorized_user(['read exam slot', 'delete exam slot']);

        try {
            Livewire::test(ListExamSlotsTable::class, ['exam' => $theirExam])->call('deleteSlot', $theirs->id);
            $this->fail('Another school\'s exam slot was reached.');
        } catch (ModelNotFoundException) {
        }

        $this->assertModelExists($theirs);
    }
}
