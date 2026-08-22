<?php

namespace Database\Seeders;

use App\Actions\Authorization\GrantSystemRole;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $school = School::findOrFail(1);

        // Role assignments are school-scoped, so name the school first.
        app(PermissionRegistrar::class)->setPermissionsTeamId($school->id);

        $join = fn (User $user) => app(GrantSchoolMembership::class)->grant($user, $school, true);

        $superAdmin = User::firstOrCreate([
            'id'                => 1,
            'name'              => 'John Doe',
            'email'             => 'super@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'super admin street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',
        ]);

        $join($superAdmin);
        app(GrantSystemRole::class)->grant($superAdmin, Role::PlatformAdmin);

        $admin = User::firstOrCreate([
            'id'                => 2,
            'name'              => 'Jane Doe',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'admin street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',

        ]);

        $join($admin);
        $admin->assignRole('admin');

        $teacher = User::create([
            'id'                => 3,
            'name'              => 'John Doe',
            'email'             => 'teacher@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'teacher street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',

        ]);

        $join($teacher);
        $teacher->assignRole('teacher');

        $teacher->teacherRecord()->create([
            'user_id' => $teacher->id,
        ]);

        $student = User::create([
            'id'                => 4,
            'name'              => 'Jane Doe',
            'email'             => 'student@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'student street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',
        ]);
        $student->studentRecord()->create([
            'admission_date'   => '22/04/04',
            'status'           => EnrollmentStatus::Active,
            'admission_number' => Str::random(10),
        ]);

        $join($student);
        $student->assignRole('student');

        $parent = User::create([
            'name'              => 'John Doe',
            'email'             => 'parent@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'parent street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',

        ]);

        $join($parent);
        $parent->assignRole('parent');

        $parent->parentRecord()->create();

        $accountant = User::create([
            'name'              => 'Jane Doe',
            'email'             => 'accountant@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'accountant street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',

        ]);

        $join($accountant);
        $accountant->assignRole('accountant');

        $librarian = User::create([
            'name'              => 'John Doe',
            'email'             => 'libratian@example.com',
            'password'          => Hash::make('password'),
            'address'           => 'librarian street',
            'birthday'          => '22/04/04',
            'nationality'       => 'nigeria',
            'state'             => 'lagos',
            'city'              => 'lagos',
            'email_verified_at' => now(),
            'gender'            => 'male',

        ]);

        $join($librarian);
        $librarian->assignRole('librarian');
    }
}
