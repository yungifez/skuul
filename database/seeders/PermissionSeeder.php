<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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

        /**
         * assign permissions to roles
        */

         //assign permissions to admin

         //assign permissions to teacher

         //assign permissions to student

        //assign permissions to parent

        //assign permissions to librarian

        //assign permissions to accountant
    }
}
