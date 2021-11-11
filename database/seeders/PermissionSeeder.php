<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        /**
         * Create all permissions
        */

        // Permissions for school
        Permission::create([
            'name' => 'create school'
        ]);
        Permission::create([
            'name' => 'edit school'
        ]);
        Permission::create([
            'name' => 'delete school'
        ]);
        Permission::create([
            'name' => 'view schools'
        ]);
        Permission::create([
            'name' => 'manage school settings'
        ]);

        //header permissions (for controlling the menu headers)
        Permission::create([
            'name' => 'header-administrate'
        ]);

        /**
         * assign permissions to roles
        */

         //assign permissions to admin
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
            'header-administrate',
            'manage school settings'
        ]);

         //assign permissions to teacher

         //assign permissions to student

        //assign permissions to parent

        //assign permissions to librarian

        //assign permissions to accountant
    }
}
