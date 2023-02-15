<?php

namespace Tests\Feature;

use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    //test unauthorized user can not view all notices

    public function test_unauthorized_user_can_not_view_all_notices()
    {
        $this->unauthorized_user()
            ->get('dashboard/notices')
            ->assertForbidden();
    }

    //test authorized user can view all notices

    public function test_authorized_user_can_view_all_notices()
    {
        $this->authorized_user(['read notice'])
            ->get('dashboard/notices')
            ->assertSuccessful();
    }

    //asser user cannot view create notice

    public function test_unauthorized_user_can_not_view_create_notice()
    {
        $this->unauthorized_user()
            ->get('dashboard/notices/create')
            ->assertForbidden();
    }

    //assert user can view create notice

    public function test_authorized_user_can_view_create_notice()
    {
        $this->authorized_user(['create notice'])
            ->get('dashboard/notices/create')
            ->assertSuccessful();
    }

    //assert unauthorized user can not create notice

    public function test_unauthorized_user_can_not_create_notice()
    {
        $this->unauthorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title'      => 'test',
                'content'    => 'test',
                'start_date' => '2019-01-01',
                'stop_date'  => '2019-01-02',
            ])->assertForbidden();
    }

    //assert user can create notice

    public function test_authorized_user_can_create_notice()
    {
        $response = $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title'      => 'Test Notice',
                'content'    => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date'  => '2019-01-02',
            ]);

        $response->assertRedirect() && $this->assertDatabaseHas('notices', [
            'title'      => 'Test Notice',
            'content'    => 'Test Description',
            'start_date' => '2019-01-01',
            'stop_date'  => '2019-01-02',
        ]);
    }

    //assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title'      => '',
                'content'    => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date'  => '2019-01-02',
            ])
            ->assertSessionHasErrors();
    }

    //assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data_2()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title'      => 'Test Notice',
                'content'    => '',
                'start_date' => '2019-01-01',
                'stop_date'  => '2019-01-01',
            ])
            ->assertSessionHasErrors();
    }

    //assert user can not create notice with invalid data

    public function test_authorized_user_can_not_create_notice_with_invalid_data_3()
    {
        $this->authorized_user(['create notice'])
            ->post('dashboard/notices', [
                'title'      => 'Test Notice',
                'content'    => 'Test Description',
                'start_date' => '2019-01-01',
                'stop_date'  => '2018-01-01',
            ])
            ->assertSessionHasErrors();
    }
}
