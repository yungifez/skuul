<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamRecordTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    //test unauthorized user cannot view exam records

    public function test_unauthorized_user_can_not_see_all_exam_records()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/exam-records')
            ->assertForbidden();
    }

    //test authorized user can see all exam records

    public function test_authorized_user_can_see_all_exam_records()
    {
        $this->authorized_user(['read exam record'])
             ->get('/dashboard/exams/exam-records')
             ->assertSuccessful();
    }

    // test unauthorized user cannot create exam record

    public function test_unauthorized_user_cant_create_exam_record()
    {
        $this->unauthorized_user()
                ->post('/dashboard/exams/exam-records', [])
                ->assertForbidden();
    }

    // test authorized user can create exam record

    public function test_authorized_user_can_create_exam_record()
    {
        $this->authorized_user(['create exam record'])
            ->post('/dashboard/exams/exam-records', [
                'user_id'      => 4,
                'section_id'   => 1,
                'subject_id'   => 1,
                'exam_records' => [
                    0 => [
                        'exam_slot_id'  => 1,
                        'student_marks' => 10,
                    ],
                    1 => [
                        'exam_slot_id'  => 2,
                        'student_marks' => 20,
                    ],
                ],
            ])->assertRedirect() && $this->assertDatabaseHas('exam_records', [
                'user_id'       => 4,
                'section_id'    => 1,
                'subject_id'    => 1,
                'exam_slot_id'  => 1,
                'student_marks' => 10,
            ]);
    }
}
