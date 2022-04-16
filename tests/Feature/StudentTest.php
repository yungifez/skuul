<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Promotion;
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

    //test unauthorized user annot view all promotions

    public function test_unauthorized_user_cannot_view_all_promotions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard/students/promotions');

        $response->assertForbidden();
    }

    //test authorized user can view all promotions

    public function test_authorized_user_can_view_all_promotions()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['read promotion']);
        $this->actingAs($user);

        $response = $this->get('/dashboard/students/promotions');

        $response->assertOk();
    }

    //test unauthorized user cannot view promotion

    public function test_unauthorized_user_cannot_view_promotion()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $promotion = Promotion::factory()->create();

        $response = $this->get("/dashboard/students/promotions/$promotion->id");

        $response->assertForbidden();
    }

    //test authorized user can view promotion

    public function test_authorized_user_can_view_promotion()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['read promotion']);
        $this->actingAs($user);

        $promotion = Promotion::factory()->create();

        $response = $this->get("/dashboard/students/promotions/$promotion->id");

        $response->assertOk();
    }

    //tes unauthorized user cannot view promoteview

    public function test_unauthorized_user_cannot_view_promoteview()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get("/dashboard/students/promote");

        $response->assertForbidden();
    }

    //test authorized user can view promoteview

    public function test_authorized_user_can_view_promoteview()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['promote student']);
        $this->actingAs($user);
        $response = $this->get("/dashboard/students/promote");

        $response->assertOk();
    }

    //test unauthorized user cannot promote students

    public function test_unauthorized_user_cannot_promote_students()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 2,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->post("/dashboard/students/promote", [
            'student_id' =>[ $student->id],
            'old_class_id' => 1,
            'old_section_id' => 2,
            'new_class_id' => 1,
            'new_section_id' => 1,
        ]);

        $promotion = Promotion::where([
            'old_class_id' => 1,
            'old_section_id' => 2,
            'new_class_id' => 1,
            'new_section_id' => 1,
        ])->whereJsonContains('students' , [$student->id])->first();

        $response->assertForbidden();
    }

    //test authorized user can promote students

    public function test_authorized_user_can_promote_students()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['promote student']);
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 2,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->post("/dashboard/students/promote", [
            'student_id' =>[ $student->id],
            'old_class_id' => 1,
            'old_section_id' => 2,
            'new_class_id' => 1,
            'new_section_id' => 1,
        ]);

        $promotion = Promotion::where([
            'old_class_id' => 1,
            'old_section_id' => 2,
            'new_class_id' => 1,
            'new_section_id' => 1,
        ])->whereJsonContains('students' , [$student->id])->first();

        $this->assertModelExists($promotion);
    }

    //test unauthorized user cannot delete promotion

    public function test_unauthorized_user_cannot_delete_promotion()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $promotion = Promotion::factory()->create();

        $response = $this->delete("/dashboard/students/promotions/$promotion->id/reset");

        $response->assertForbidden();
    }

    //test authorized user can delete promotion

    public function test_authorized_user_can_delete_promotion()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['reset promotion']);
        $this->actingAs($user);

        $promotion = Promotion::factory()->create();

        $response = $this->delete("/dashboard/students/promotions/$promotion->id/reset");

        $this->assertModelMissing($promotion);
    }

    //test unauthorized user cannot view all graduations

    public function test_unauthorized_user_cannot_view_all_graduations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/dashboard/students/graduations');

        $response->assertForbidden();
    }

    //test authorized user can view all graduations

    public function test_authorized_user_can_view_all_graduations()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view graduations']);
        $this->actingAs($user);

        $response = $this->get('/dashboard/students/graduations');

        $response->assertOk();
    }

    //test unauthorized user cannot graduate student

    public function test_unauthorized_user_cannot_graduate_student()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $student = User::factory()->create();
        $student->assignRole('student');
        $student->studentRecord()->create([
            'my_class_id' => 1,
            'section_id' => 2,
            'admission_date' => '22/04/04',
            'is_graduated' => false,
        ]);
        $response = $this->post("/dashboard/students/graduate", [
            'student_id' =>[ $student->id]
        ]);

        $response->assertForbidden();
    }
}
