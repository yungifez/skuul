<?php

namespace Database\Seeders;

use App\Enums\Role as RoleName;
use Illuminate\Database\Seeder;
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
        Role::firstOrCreate([
            'name'      => RoleName::PlatformAdmin,
            'school_id' => null,
        ]);
        Role::firstOrCreate([
            'name'      => RoleName::OrganizationAdmin,
            'school_id' => null,
        ]);
        Role::firstOrCreate([
            'name' => 'admin',
        ]);
        Role::firstOrCreate([
            'name' => 'teacher',
        ]);
        Role::firstOrCreate([
            'name' => 'student',
        ]);
        Role::firstOrCreate([
            'name' => 'parent',
        ]);
        Role::firstOrCreate([
            'name' => 'accountant',
        ]);
        Role::firstOrCreate([
            'name' => 'librarian',
        ]);
    }
}
