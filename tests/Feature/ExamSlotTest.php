<?php

namespace Tests\Feature;

use App\Models\ExamSlot;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamSlotTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    //test unauthorized user can not see all exam slots

    public function test_unauthorized_user_can_not_see_all_exam_slots()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/manage/exam-slots')
            ->assertForbidden();
    }

    //test authorized user can see all exam slots

    public function test_authorized_user_can_see_all_exam_slots()
    {
        $this->authorized_user(['read exam slot'])
            ->get('/dashboard/exams/1/manage/exam-slots')
            ->assertSuccessful();
    }

    //test unauthorized user cannot view create exam slot

    public function test_unauthorized_user_cant_view_create_exam_slot()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/manage/exam-slots/create')
            ->assertForbidden();
    }
    //test authorized user can view create exam slot

    public function test_user_can_view_create_exam_slot()
    {
        $this->authorized_user(['create exam slot'])
            ->get('/dashboard/exams/1/manage/exam-slots/create')
            ->assertOk();
    }

    //test unauthorized user cannot create exam slot

    public function test_unauthorized_user_cant_create_exam_slot()
    {
        $this->unauthorized_user()
            ->post('/dashboard/exams/1/manage/exam-slots')
            ->assertForbidden();
    }

    //test authorized user can create exam slot

    public function test_authorized_user_can_create_exam_slot()
    {
        $response = $this->authorized_user(['create exam slot'])
            ->post('/dashboard/exams/1/manage/exam-slots', [
                'name'        => 'test exam slot',
                'description' => 'test description',
                'total_marks' => 20,
            ]);

        $this->assertDatabaseHas('exam_slots', [
            'name'        => 'test exam slot',
            'description' => 'test description',
            'total_marks' => 20,
        ]);
    }

    //test unauthorized user cannot view edit exam slot

    public function test_unauthorized_user_cant_view_edit_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->unauthorized_user()
            ->get("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id/edit")
            ->assertForbidden();
    }

    //test authorized user can view edit exam slot

    public function test_authorized_user_can_view_edit_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['update exam slot'])
            ->get("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id/edit")
            ->assertSuccessful();
    }

    //test unauthorized user cannot update exam slot

    public function test_unauthorized_user_cant_update_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->unauthorized_user()
            ->put("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id", ['name' => 'test exam slot', 'description' => 'test description', 'total_marks' => '10'])
            ->assertForbidden();

        $this->assertDatabaseMissing('exam_slots', [
            'id'          => $examSlot->id,
            'name'        => 'test exam slot',
            'description' => 'test description',
            'total_marks' => '10',
        ]);
    }

    //test authorized user can update exam slot

    public function test_authorized_user_can_update_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['update exam slot'])
            ->put("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id", ['name' => 'test exam slot', 'description' => 'test description', 'total_marks' => '10']);

        $this->assertDatabaseHas('exam_slots', [
            'id'          => $examSlot->id,
            'name'        => 'test exam slot',
            'description' => 'test description',
            'total_marks' => '10',
        ]);
    }

    //test unauthorized user cannot delete exam slot

    public function test_unauthorized_user_cant_delete_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id")
            ->assertForbidden() && $this->assertModelExists($examSlot);
    }

    //test authorized user can delete exam slot

    public function test_authorized_user_can_delete_exam_slot()
    {
        $examSlot = ExamSlot::factory()->create();
        $this->authorized_user(['delete exam slot'])
            ->delete("/dashboard/exams/{$examSlot->exam->id}/manage/exam-slots/$examSlot->id");

        $this->assertModelMissing($examSlot);
    }
}
