<?php

namespace Tests\Feature;

use App\Models\Syllabus;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SyllabusTest extends TestCase
{
    use RefreshDatabase;
    use FeatureTestTrait;

    // test unauthorized user can't view all syllabi

    public function test_unauthorized_user_cant_view_all_syllabi()
    {
        $this->unauthorized_user()
            ->get('/dashboard/syllabi')
            ->assertForbidden();
    }

    // test authorized user can view all syllabi

    public function test_authorized_user_can_view_all_syllabi()
    {
        $this->authorized_user(['read syllabus'])
            ->get('/dashboard/syllabi')
            ->assertOk();
    }

    //test unauthorized user can't view create syllabus

    public function test_unauthorized_user_cant_view_create_syllabus()
    {
        $this->unauthorized_user()
            ->get('/dashboard/syllabi/create')
            ->assertForbidden();
    }

    //test authorized user can view create syllabus

    public function test_user_can_view_create_syllabus()
    {
        $this->authorized_user(['create syllabus'])
            ->get('/dashboard/syllabi/create')
            ->assertOk();
    }

    //test unauthorized cant create syllabus

    public function test_unauthorized_user_cant_create_syllabus()
    {
        $file = Storage::fake('syllabi');
        $this->unauthorized_user()
        ->post('/dashboard/syllabi', [
            'name'        => 'Test syllabus',
            'my_class_id' => 1,
            'subject_id'  => 1,
            'description' => 'Test syllabus description',
            'file'        => UploadedFile::fake()->create('test-syllabus.pdf', 100),
        ])->assertForbidden();
    }

    //test authorized user can create syllabus

    public function test_authorized_user_can_create_syllabus()
    {
        $file = Storage::fake('syllabi');
        $this->authorized_user(['create syllabus'])
        ->post('/dashboard/syllabi', [
            'name'        => 'Test syllabus',
            'subject_id'  => 1,
            'description' => 'Test syllabus description',
            'file'        => UploadedFile::fake()->create('test-syllabus.pdf', 100),
        ]);

        $this->assertDatabaseHas('syllabi', [
            'name'        => 'Test syllabus',
            'subject_id'  => 1,
            'description' => 'Test syllabus description',
        ]);
    }

    //test unauthorized user can't delete syllabus

    public function test_unauthorized_user_cant_delete_syllabus()
    {
        $syllabus = Syllabus::factory()->create();
        $this->unauthorized_user()
            ->delete('/dashboard/syllabi/'.$syllabus->id)
            ->assertForbidden();
    }

    //test authorized user can delete syllabus

    public function test_authorized_user_can_delete_syllabus()
    {
        $syllabus = Syllabus::factory()->create();
        $this->authorized_user(['delete syllabus'])
            ->delete('/dashboard/syllabi/'.$syllabus->id);

        $this->assertModelMissing($syllabus);
    }
}
