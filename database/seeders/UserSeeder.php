<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        $superAdmin = User::create([
            'name' => 'John Doe',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'school_id' => 1,
            'address' => 'super admin street',
            'birthday' => '22/04/04',
        ]);

        $superAdmin->assignRole('super-admin');

        $admin = User::create([
            'name' => 'Jane Doe',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'school_id' => 1,
            'address' => 'admin street',
            'birthday' => '22/04/04',
        ]);

        $admin->assignRole('admin');

        $teacher = User::create([
            'name'     => 'John Doe',
            'email'    => 'teacher@teacher.com',
            'password' => Hash::make('password'),
            'school_id'=> 1,
            'address' => 'teacher street',
            'birthday' => '22/04/04',
        ]);

        $teacher->assignRole('teacher');

        $student = User::create([
            'name' => 'Jane Doe',
            'email' => 'student@student.com',
            'password' => Hash::make('password'),
            'school_id'=> 1,
            'address' => 'student street',
            'birthday' => '22/04/04',
        ]);

        $student->assignRole('student');

        $parent = User::create([
            'name' => 'John Doe',
            'email' => 'parent@parent.com',
            'password' => Hash::make('password'),
            'school_id'=> 1,
            'address' => 'parent street',
            'birthday' => '22/04/04',
        ]);

        $parent->assignRole('parent');

        $accountant = User::create([
            'name' => 'Jane Doe',
            'email' => 'accountant@accountant.com',
            'password' => Hash::make('password'),
            'school_id'=> 1,
            'address' => 'accountant street',
            'birthday' => '22/04/04',
        ]);

        $accountant->assignRole('accountant');

        $librarian = User::create([
            'name' => 'John Doe',
            'email' => 'libratian@librarian.com',
            'password' => Hash::make('password'),
            'school_id'=> 1,
            'address' => 'librarian street',
            'birthday' => '22/04/04',
        ]);

        $librarian->assignRole('librarian');

        
    }
}
