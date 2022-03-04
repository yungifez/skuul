<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        Role::create([
            'name' => 'super-admin',
        ]);
        Role::create([
            'name' => 'admin',
        ]);
        Role::create([
            'name' => 'teacher',
        ]);
        Role::create([
            'name' => 'student',
        ]);
        Role::create([
            'name' => 'parent',
        ]);
        Role::create([
            'name' => 'accountant',
        ]);
        Role::create([
            'name' => 'librarian',
        ]);
    }
}
