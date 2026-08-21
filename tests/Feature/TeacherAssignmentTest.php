<?php

namespace Tests\Feature;

use App\Actions\School\GrantSchoolMembership;
use App\Enums\Role;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A teacher is assigned to subjects of the school being worked in only.
 */
class TeacherAssignmentTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_authorized_user_can_assign_a_teacher_to_a_subject(): void
    {
        $teacher = $this->teacherOfWorkingSchool();
        $subject = Subject::factory()->create(['school_id' => current_school_id()]);

        $this->authorized_user(['update subject'])
            ->post("dashboard/subjects/assign-teacher/$teacher->id", ['subjects' => [$subject->id]])
            ->assertRedirect();

        $this->assertTrue($teacher->fresh()->subjects->contains($subject));
    }

    public function test_unauthorized_user_cannot_assign_a_teacher(): void
    {
        $teacher = $this->teacherOfWorkingSchool();
        $subject = Subject::factory()->create(['school_id' => current_school_id()]);

        $this->unauthorized_user()
            ->post("dashboard/subjects/assign-teacher/$teacher->id", ['subjects' => [$subject->id]])
            ->assertForbidden();

        $this->assertCount(0, $teacher->fresh()->subjects);
    }

    public function test_unauthorized_user_cannot_open_the_assign_teacher_screen(): void
    {
        $this->unauthorized_user()
            ->get('dashboard/subjects/assign-teacher')
            ->assertForbidden();
    }

    public function test_a_subject_of_another_school_cannot_be_assigned(): void
    {
        $other = School::factory()->create();
        $teacher = $this->teacherOfWorkingSchool();
        $theirSubject = Subject::factory()->create(['school_id' => $other->id]);

        $this->authorized_user(['update subject'])
            ->post("dashboard/subjects/assign-teacher/$teacher->id", ['subjects' => [$theirSubject->id]])
            ->assertSessionHasErrors('subjects.0');

        $this->assertCount(0, $teacher->fresh()->subjects);
    }

    public function test_a_teacher_of_another_school_cannot_be_assigned(): void
    {
        $other = School::factory()->create();
        $subject = Subject::factory()->create(['school_id' => current_school_id()]);

        $teacher = $this->nonMember();
        app(GrantSchoolMembership::class)->grant($teacher, $other, primary: true);
        school_context()->set($other, remember: false);
        $teacher->assignRole(Role::Teacher);
        school_context()->set($this->workingSchool(), remember: false);

        $this->authorized_user(['update subject'])
            ->post("dashboard/subjects/assign-teacher/$teacher->id", ['subjects' => [$subject->id]])
            ->assertNotFound();

        $this->assertCount(0, $teacher->fresh()->subjects);
    }

    /**
     * Create a teacher who works in the school being worked in.
     */
    private function teacherOfWorkingSchool(): User
    {
        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher);

        return $teacher->fresh();
    }
}
