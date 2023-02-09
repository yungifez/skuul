<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    //test unauthorized user cannot view all exams

    public function test_unauthorized_user_cant_view_all_exams()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams')
            ->assertForbidden();
    }

    //test authorized user can view all exams

    public function test_authorized_user_can_view_all_exams()
    {
        $this->authorized_user(['read exam'])
            ->get('/dashboard/exams')
            ->assertOk();
    }

    //test unauthorized user cannot view create exam

    public function test_unauthorized_user_cant_view_create_exam()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/create')
            ->assertForbidden();
    }

    //test authorized user can view create exam

    public function test_user_can_view_create_exam()
    {
        $this->authorized_user(['create exam'])
            ->get('/dashboard/exams/create')
            ->assertOk();
    }

    //test unauthorized user cannot create exam

    public function test_unauthorized_user_cant_create_exam()
    {
        $this->unauthorized_user()
            ->post('/dashboard/exams')
            ->assertForbidden();
    }

    //test authorized user can create exam

    public function test_authorized_user_can_create_exam()
    {
        $this->authorized_user(['create exam'])
            ->post('/dashboard/exams', [
                'name'        => 'test exam',
                'semester_id' => '1',
                'description' => 'test description',
                'start_date'  => '2020-01-01',
                'stop_date'   => '2020-01-01',
            ]);

        $this->assertDatabaseHas('exams', [
            'name'        => 'test exam',
            'semester_id' => '1',
            'description' => 'test description',
            'start_date'  => '2020-01-01',
            'stop_date'   => '2020-01-01',
        ]);
    }

    //test unauthorized user cannot view edit exam

    public function test_unauthorized_user_cant_view_edit_exam()
    {
        $this->unauthorized_user()
            ->get('/dashboard/exams/1/edit')
            ->assertForbidden();
    }

    //test authorized user can view edit exam

    public function test_user_can_view_edit_exam()
    {
        $this->authorized_user(['update exam'])
            ->get('/dashboard/exams/1/edit')
            ->assertOk();
    }

    //test unauthorized user cannot update exam

    public function test_unauthorized_user_cant_update_exam()
    {
        $exam = Exam::factory()->create();
        $this->unauthorized_user()
            ->put("/dashboard/exams/$exam->id", [
                'name'        => 'test',
                'semester_id' => '1',
                'description' => 'test',
                'start_date'  => '2018-01-01',
                'stop_date'   => '2018-01-01',
            ])
            ->assertForbidden();
    }

    //test authorized user can update exam

    public function test_authorized_user_can_update_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['update exam'])
            ->put("/dashboard/exams/$exam->id", [
                'name'        => 'test',
                'semester_id' => '1',
                'description' => 'test',
                'start_date'  => '2018-01-01',
                'stop_date'   => '2018-01-02',
            ]);

        $this->assertDatabaseHas('exams', [
            'id'          => $exam->id,
            'name'        => 'test',
            'semester_id' => '1',
            'description' => 'test',
            'start_date'  => '2018-01-01',
            'stop_date'   => '2018-01-02',
        ]);
    }

    //test unauthorized user cannot view exam

    public function test_unauthorized_user_cannot_view_exam()
    {
        $exam = Exam::factory()->create();
        $this->unauthorized_user()
            ->get("dashboard/exams/$exam->id/edit")
            ->assertForbidden();
    }

    //test unauthorized user cannot view exam

    public function test_authorized_user_can_view_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['read exam'])
            ->get("dashboard/exams/$exam->id/edit")
            ->assertForbidden();
    }

    //test unauthorized user cannot view exam

    public function test_unauthorized_user_cannot_delete_exam()
    {
        $exam = Exam::factory()->create();
        $this->unauthorized_user()
            ->delete("dashboard/exams/$exam->id")
            ->assertForbidden();
    }

    //test unauthorized user cannot view exam

    public function test_authorized_user_can_delete_exam()
    {
        $exam = Exam::factory()->create();
        $this->authorized_user(['delete exam'])
            ->delete("dashboard/exams/$exam->id");

        $this->assertModelMissing($exam);
    }

    //test authorized user can view exam tabulation

    public function test_authorized_user_can_view_exam_tabulation()
    {
        $this->authorized_user(['read exam'])
            ->get('dashboard/exams/tabulation-sheet')
            ->assertSuccessful();
    }

    //test authorized user can view exam tabulation

    public function test_authorized_user_can_view_semester_result_tabulation()
    {
        $this->authorized_user(['read exam'])
            ->get('dashboard/exams/semester-result-tabulation')
            ->assertSuccessful();
    }

    //test authorized user can view exam tabulation

    public function test_authorized_user_can_view_academic_year_result_tabulation()
    {
        $this->authorized_user(['read exam'])
              ->get('dashboard/exams/academic-year-result-tabulation')
              ->assertSuccessful();
    }

    //test authorized user can view exam tabulation

    public function test_authorized_user_can_view_result_checker()
    {
        $this->authorized_user(['check result'])
            ->get('dashboard/exams/result-checker')
            ->assertSuccessful();
    }
}
