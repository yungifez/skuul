<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentTest extends TestCase
{
    //test view all students cannot be accessed by unauthorised users
    
    public function test_view_all_students_cannot_be_accessed_by_unauthorised_users()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/students');

        $response->assertForbidden();
    }

    //test view all students can be accessed by authorised users

    public function test_view_all_students_can_be_accessed_by_authorised_users()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['read student']);
        $this->actingAs($user);
        $response = $this->get('/dashboard/students');

        $response->assertOk();
    }

    //test create student cannot be accessed by unauthorised users

    public function test_create_student_cannot_be_accessed_by_unauthorised_users()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/dashboard/students/create');

        $response->assertForbidden();
    }

    //test create student can be accessed by authorised users

    public function test_create_student_can_be_accessed_by_authorised_users()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create student']);
        $this->actingAs($user);
        $response = $this->get('/dashboard/students/create');

        $response->assertOk();
    }

    //test unauthorised users cannot create students

    public function test_unauthorised_users_cannot_create_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/dashboard/students', [
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => 'test@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'blood_group' => 'a+',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '2004/04/22',
        ]);

        $response->assertForbidden();
    }

    //test user can create student

    public function test_user_can_create_student()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['create student']);
        $this->actingAs($user);
        $response = $this->post('/dashboard/students', [
            'first_name' => 'Test',
            'last_name' => 'Student',
            'other_name' => '',
            'email' => 'test@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'blood_group' => 'a+',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '2004/04/22',
        ]);
        $student = User::where('email', 'test@test.test')->first();
        $studentRecord = $student->studentRecord;
    
        $this->assertModelExists($student) && $this->assertModelExists($studentRecord);
    }

    //test edit student cannot be accessed by unauthorised users

    public function test_edit_student_cannot_be_accessed_by_unauthorised_users()
    {
        $user = User::factory()->create();
        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $this->actingAs($user);
        $response = $this->get("/dashboard/students/$student->id/edit");

        $response->assertForbidden();
    }

    //test edit student can be accessed by authorised users

    public function test_edit_student_can_be_accessed_by_authorised_users()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['update student']);
        $this->actingAs($user);
        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->get("/dashboard/students/$student->id/edit");

        $response->assertOk();
    }

    //test unauthorised users cannot edit students

    public function test_unauthorised_users_cannot_edit_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->patch("/dashboard/students/$student->id", [
            'first_name' => 'Test',
            'last_name' => 'Student 2',
            'email' => 'test@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'blood_group' => 'a+',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '2004/04/22',
        ]);

        $response->assertForbidden();
    }

    
    //test authorised users can edit students

    public function test_authorised_users_can_edit_students()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['update student']);
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->put("/dashboard/students/$student->id", [
            'first_name' => 'Test 2',
            'other_names' => 'Student 2',
            'last_name' => 'Student',
            'email' => 'test2@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'nationality' => 'nigeria',
            'state' => 'lagos',
            'city' => 'lagos',
            'blood_group' => 'a+',
            'address' => 'test address',
            'birthday' => '2004/04/22',
            'phone' => '08080808080',
        ]);

        $student = User::where('email', 'test2@test.test')->first();

        $this->assertModelExists($student);
    }

    //test unauthorised users cannot delete students

    public function test_unauthorised_users_cannot_delete_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->delete("/dashboard/students/$student->id");

        $response->assertForbidden();
    }

    //test authorised users can delete students

    public function test_authorised_users_can_delete_students()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['delete student']);
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 1,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->delete("/dashboard/students/$student->id");

        $this->assertModelMissing($student);
    }
}
