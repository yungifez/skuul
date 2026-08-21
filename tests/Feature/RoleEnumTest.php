<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role as RoleRecord;
use Tests\TestCase;

/**
 * The built-in access profiles are named in one place.
 *
 * Business rules must read permissions. Where the application still names a
 * profile it uses App\Enums\Role, so a renamed seed cannot break the code
 * without failing here first.
 */
class RoleEnumTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_every_built_in_profile_exists_as_a_role(): void
    {
        foreach (Role::cases() as $role) {
            $this->assertTrue(
                RoleRecord::where('name', $role->value)->exists(),
                "The role {$role->value} is missing from the database."
            );
        }
    }

    public function test_a_role_can_be_assigned_and_read_with_the_enum(): void
    {
        $user = $this->memberOf($this->workingSchool());

        $user->assignRole(Role::Teacher);

        $this->assertTrue($user->fresh()->hasRole(Role::Teacher));
        $this->assertFalse($user->fresh()->hasRole(Role::Student));
    }

    public function test_the_student_scope_reads_the_student_profile(): void
    {
        $student = $this->memberOf($this->workingSchool());
        $student->assignRole(Role::Student);

        $teacher = $this->memberOf($this->workingSchool());
        $teacher->assignRole(Role::Teacher);

        $students = User::students()->pluck('id');

        $this->assertTrue($students->contains($student->id));
        $this->assertFalse($students->contains($teacher->id));
    }
}
