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
            'name' => 'read school'
        ]);
        Permission::create([
            'name' => 'update school'
        ]);
        Permission::create([
            'name' => 'delete school'
        ]);
        Permission::create([
            'name' => 'manage school settings'
        ]);

        // Permissions for class group
        Permission::create([
            'name' => 'create class group'
        ]);
        Permission::create([
            'name' => 'read class group'
        ]);
        Permission::create([
            'name' => 'update class group'
        ]);
        Permission::create([
            'name' => 'delete class group'
        ]);

        // Permissions for class
        Permission::create([
            'name' => 'create class'
        ]);
        Permission::create([
            'name' => 'read class'
        ]);
        Permission::create([
            'name' => 'update class'
        ]);
        Permission::create([
            'name' => 'delete class'
        ]);

        // Permissions for sectionm
        Permission::create([
            'name' => 'create section'
        ]);
        Permission::create([
            'name' => 'read section'
        ]);
        Permission::create([
            'name' => 'update section'
        ]);
        Permission::create([
            'name' => 'delete section'
        ]);

        //Permission for students
        Permission::create([
            'name' => 'create student'
        ]);
        Permission::create([
            'name' => 'read student'
        ]);
        Permission::create([
            'name' => 'update student'
        ]);
        Permission::create([
            'name' => 'delete student'
        ]);

        //Permission for academic year
        Permission::create([
            'name' => 'create academic year'
        ]);
        Permission::create([
            'name' => 'read academic year'
        ]);
        Permission::create([
            'name' => 'update academic year'
        ]);
        Permission::create([
            'name' => 'delete academic year'
        ]);

         //Permission for teacher
         Permission::create([
            'name' => 'create teacher'
        ]);
        Permission::create([
            'name' => 'read teacher'
        ]);
        Permission::create([
            'name' => 'update teacher'
        ]);
        Permission::create([
            'name' => 'delete teacher'
        ]);
        
        //Permission for subject
        Permission::create([
            'name' => 'create subject'
        ]);
        Permission::create([
            'name' => 'read subject'
        ]);
        Permission::create([
            'name' => 'update subject'
        ]);
        Permission::create([
            'name' => 'delete subject'
        ]);
                


        //header permissions (for controlling the menu headers)
        Permission::create([
            'name' => 'header-administrate'
        ]);
        Permission::create([
            'name' => 'header-schools'
        ]);
        Permission::create([
            'name' => 'menu-class'
        ]);
        Permission::create([
            'name' => 'menu-section'
        ]);
        Permission::create([
            'name' => 'menu-student'
        ]);
        Permission::create([
            'name' => 'menu-teacher'
        ]);
        Permission::create([
            'name' => 'menu-academic-year'
        ]);



        /**
         * assign permissions to roles
        */

         //assign permissions to admin
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
            'header-administrate',
            'menu-section',
            'menu-class',
            'menu-student',
            'menu-teacher',
            'menu-academic-year',
            'manage school settings',
            'create section',
            'read section',
            'update section',
            'delete section',
            'create class',
            'read class',
            'update class',
            'delete class',
            'create class group',
            'read class group',
            'update class group',
            'delete class group',
            'create student',
            'read student',
            'update student',
            'delete student',
            'create academic year',
            'read academic year',
            'update academic year',
            'delete academic year',
            'create teacher',
            'read teacher',
            'update teacher',
            'delete teacher',
            'create subject',
            'read subject',
            'update subject',
            'delete subject',
        ]);

         //assign permissions to teacher

         //assign permissions to student

        //assign permissions to parent

        //assign permissions to librarian

        //assign permissions to accountant
    }
}
