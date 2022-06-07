<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeSystemTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    //test unauthorized user can not see all grade systems

    public function test_unauthorized_user_can_not_see_all_grade_systems()
    {
        $this->unauthorized_user()
            ->get('/dashboard/grade-systems')
            ->assertForbidden();
    }

    //test authorized user can see all grade systems

    public function test_authorized_user_can_see_all_grade_systems()
    {
        $this->authorized_user(['read grade system'])
            ->get('/dashboard/grade-systems')
            ->assertSuccessful();
    }

    //test unauthorized user can not see create grade system

    public function test_unauthorized_user_can_not_see_create_grade_system()
    {
        $this->unauthorized_user()
            ->get('/dashboard/grade-systems/create')
            ->assertForbidden();
    }

    //test authorized user can see create grade system

    public function test_authorized_user_can_see_create_grade_system()
    {
        $this->authorized_user(['create grade system'])
            ->get('/dashboard/grade-systems/create')
            ->assertOk();
    }

    //test unauthorized user can not create grade system

    public function test_unauthorized_user_can_not_create_grade_system()
    {
        $this->unauthorized_user()
            ->post('/dashboard/grade-systems')
            ->assertForbidden();
    }

    //test authorized user can create grade system

    public function test_authorized_user_can_create_grade_system()
    {
        $this->authorized_user(['create grade system'])
            ->post('/dashboard/grade-systems', [
                'name'           => 'test grade',
                'remark'         => 'test remarks',
                'grade_from'     => '0',
                'grade_till'     => '10',
                'class_group_id' => '1',
            ]);

        $this->assertDatabaseHas('grade_systems', [
            'name'           => 'test grade',
            'remark'         => 'test remarks',
            'grade_from'     => '0',
            'grade_till'     => '10',
            'class_group_id' => '1',
        ]);
    }

    //test unauthorized user can not see edit grade system

    public function test_unauthorized_user_can_not_see_edit_grade_system()
    {
        $this->unauthorized_user()
            ->get('/dashboard/grade-systems/1/edit')
            ->assertForbidden();
    }

    //test authorized user can see edit grade system

    public function test_authorized_user_can_see_edit_grade_system()
    {
        $this->authorized_user(['update grade system'])
            ->get('/dashboard/grade-systems/1/edit')
            ->assertOk();
    }

    //test unauthorized user can not update grade system

    public function test_unauthorized_user_can_not_update_grade_system()
    {
        $this->unauthorized_user()
            ->put('/dashboard/grade-systems/1')
            ->assertForbidden();
    }

    //test authorized user can update grade system

    public function test_authorized_user_can_update_grade_system()
    {
        $this->authorized_user(['update grade system'])
            ->put('/dashboard/grade-systems/1', [
                'name'           => 'test grade',
                'remark'         => 'test remarks',
                'grade_from'     => '90',
                'grade_till'     => '100',
                'class_group_id' => '1',
            ]);

        $this->assertDatabaseHas('grade_systems', [
            'id'             => '1',
            'name'           => 'test grade',
            'remark'         => 'test remarks',
            'grade_from'     => '90',
            'grade_till'     => '100',
            'class_group_id' => '1',
        ]);
    }

    //test unauthorized user can not delete grade system

    public function test_unauthorized_user_can_not_delete_grade_system()
    {
        $this->unauthorized_user()
            ->delete('/dashboard/grade-systems/1')
            ->assertForbidden();
    }

    //test authorized user can delete grade system

    public function test_authorized_user_can_delete_grade_system()
    {
        $this->authorized_user(['delete grade system'])
            ->delete('/dashboard/grade-systems/1');

        $this->assertDatabaseMissing('grade_systems', [
            'id' => '1',
        ]);
    }
}
