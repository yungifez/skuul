<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
         * Create all permissions.
         *
         * EVERYTHING HERE IS USED IN A SINGULAR SENSE
         */

        // Permissions for school
        Permission::firstOrCreate([
            'name' => 'create school',
        ]);
        Permission::firstOrCreate([
            'name' => 'read school',
        ]);
        Permission::firstOrCreate([
            'name' => 'update school',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete school',
        ]);
        Permission::firstOrCreate([
            'name' => 'manage school settings',
        ]);

        // Permissions for class group
        Permission::firstOrCreate([
            'name' => 'create class group',
        ]);
        Permission::firstOrCreate([
            'name' => 'read class group',
        ]);
        Permission::firstOrCreate([
            'name' => 'update class group',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete class group',
        ]);

        // Permissions for class
        Permission::firstOrCreate([
            'name' => 'create class',
        ]);
        Permission::firstOrCreate([
            'name' => 'read class',
        ]);
        Permission::firstOrCreate([
            'name' => 'update class',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete class',
        ]);

        // Permissions for sectionm
        Permission::firstOrCreate([
            'name' => 'create section',
        ]);
        Permission::firstOrCreate([
            'name' => 'read section',
        ]);
        Permission::firstOrCreate([
            'name' => 'update section',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete section',
        ]);

        //Permission for students
        Permission::firstOrCreate([
            'name' => 'create student',
        ]);
        Permission::firstOrCreate([
            'name' => 'read student',
        ]);
        Permission::firstOrCreate([
            'name' => 'update student',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete student',
        ]);

        //Permission for academic year
        Permission::firstOrCreate([
            'name' => 'create academic year',
        ]);
        Permission::firstOrCreate([
            'name' => 'read academic year',
        ]);
        Permission::firstOrCreate([
            'name' => 'update academic year',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete academic year',
        ]);
        Permission::firstOrCreate([
            'name' => 'set academic year',
        ]);

        //Permission for teacher
        Permission::firstOrCreate([
            'name' => 'create teacher',
        ]);
        Permission::firstOrCreate([
            'name' => 'read teacher',
        ]);
        Permission::firstOrCreate([
            'name' => 'update teacher',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete teacher',
        ]);

        //Permission for parent
        Permission::firstOrCreate([
            'name' => 'create parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'read teacher',
        ]);
        Permission::firstOrCreate([
            'name' => 'update parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete parent',
        ]);

        //Permission for subject
        Permission::firstOrCreate([
            'name' => 'create subject',
        ]);
        Permission::firstOrCreate([
            'name' => 'read subject',
        ]);
        Permission::firstOrCreate([
            'name' => 'update subject',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete subject',
        ]);

        //Permission for student promotions
        Permission::firstOrCreate([
            'name' => 'promote student',
        ]);
        Permission::firstOrCreate([
            'name' => 'read promotion',
        ]);
        Permission::firstOrCreate([
            'name' => 'reset promotion',
        ]);

        //permission for graduation
        Permission::firstOrCreate([
            'name' => 'graduate student',
        ]);
        Permission::firstOrCreate([
            'name' => 'view graduations',
        ]);
        Permission::firstOrCreate([
            'name' => 'reset graduation',
        ]);

        //permission for semesters
        Permission::firstOrCreate([
            'name' => 'create semester',
        ]);
        Permission::firstOrCreate([
            'name' => 'read semester',
        ]);
        Permission::firstOrCreate([
            'name' => 'update semester',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete semester',
        ]);

        Permission::firstOrCreate([
            'name' => 'set semester',
        ]);

        //permission for syllabus
        Permission::firstOrCreate([
            'name' => 'create syllabus',
        ]);
        Permission::firstOrCreate([
            'name' => 'read syllabus',
        ]);
        Permission::firstOrCreate([
            'name' => 'update syllabus',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete syllabus',
        ]);

        //permission for timetable
        Permission::firstOrCreate([
            'name' => 'create timetable',
        ]);
        Permission::firstOrCreate([
            'name' => 'read timetable',
        ]);
        Permission::firstOrCreate([
            'name' => 'update timetable',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete timetable',
        ]);

        //exam permissions
        Permission::firstOrCreate([
            'name' => 'create exam',
        ]);
        Permission::firstOrCreate([
            'name' => 'read exam',
        ]);
        Permission::firstOrCreate([
            'name' => 'update exam',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete exam',
        ]);

        //permission for grade system
        Permission::firstOrCreate([
            'name' => 'create grade system',
        ]);
        Permission::firstOrCreate([
            'name' => 'read grade system',
        ]);
        Permission::firstOrCreate([
            'name' => 'update grade system',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete grade system',
        ]);

        //permission for exam slots
        Permission::firstOrCreate([
            'name' => 'create exam slot',
        ]);
        Permission::firstOrCreate([
            'name' => 'read exam slot',
        ]);
        Permission::firstOrCreate([
            'name' => 'update exam slot',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete exam slot',
        ]);

        //permission for exam records
        Permission::firstOrCreate([
            'name' => 'create exam record',
        ]);
        Permission::firstOrCreate([
            'name' => 'read exam record',
        ]);
        Permission::firstOrCreate([
            'name' => 'update exam record',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete exam record',
        ]);

        //check result permission
        Permission::firstOrCreate([
            'name' => 'check result',
        ]);

        //permission for notices

        Permission::firstOrCreate([
            'name' => 'create notice',
        ]);

        Permission::firstOrCreate([
            'name' => 'read notice',
        ]);

        Permission::firstOrCreate([
            'name' => 'update notice',
        ]);

        Permission::firstOrCreate([
            'name' => 'delete notice',
        ]);

        //header permissions (for controlling the menu headers)
        Permission::firstOrCreate([
            'name' => 'header-administrate',
        ]);
        Permission::firstOrCreate([
            'name' => 'header-schools',
        ]);
        Permission::firstOrCreate([
            'name' => 'header-academics',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-class',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-section',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-student',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-teacher',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-academic-year',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-subject',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-syllabus',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-timetable',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-exam',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-grade-system',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-notice',
        ]);
        /**
         * assign permissions to roles.
         */

        //assign permissions to admin
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
            'header-administrate',
            'header-academics',
            'menu-section',
            'menu-class',
            'menu-student',
            'menu-teacher',
            'menu-academic-year',
            'menu-subject',
            'menu-syllabus',
            'menu-timetable',
            'menu-exam',
            'menu-grade-system',
            'menu-notice',
            'menu-parent',
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
            'set academic year',
            'create teacher',
            'read teacher',
            'update teacher',
            'delete teacher',
            'create subject',
            'read subject',
            'update subject',
            'delete subject',
            'promote student',
            'read promotion',
            'reset promotion',
            'graduate student',
            'view graduations',
            'reset graduation',
            'create semester',
            'read semester',
            'update semester',
            'delete semester',
            'set semester',
            'create syllabus',
            'read syllabus',
            'update syllabus',
            'delete syllabus',
            'create timetable',
            'read timetable',
            'update timetable',
            'delete timetable',
            'create exam',
            'read exam',
            'update exam',
            'delete exam',
            'create grade system',
            'read grade system',
            'update grade system',
            'delete grade system',
            'create exam slot',
            'read exam slot',
            'update exam slot',
            'delete exam slot',
            'create exam record',
            'read exam record',
            'update exam record',
            'delete exam record',
            'create notice',
            'read notice',
            'update notice',
            'delete notice',
            'check result',
        ]);

        //assign permissions to teacher
        $teacher = Role::where('name', 'teacher')->first();
        $teacher->givePermissionTo([
            'header-academics',
            'menu-syllabus',
            'menu-timetable',
            'menu-exam',
            'menu-notice',
            'create syllabus',
            'read syllabus',
            'update syllabus',
            'delete syllabus',
            'create timetable',
            'read timetable',
            'update timetable',
            'delete timetable',
            'create exam record',
            'read exam record',
            'update exam record',
            'delete exam record',
            'read notice',
            'check result',
        ]);

        //assign permissions to student
        $student = Role::where('name', 'student')->first();
        $student->givePermissionTo([
            'header-academics',
            'menu-syllabus',
            'menu-timetable',
            'menu-notice',
            'menu-exam',
            'read syllabus',
            'read timetable',
            'read notice',
            'check result',
        ]);
        //assign permissions to parent
        $parent = Role::where('name', 'parent')->first();
        $parent->givePermissionTo([
            'header-academics',
            'menu-syllabus',
            'menu-timetable',
            'menu-notice',
            'menu-exam',
            'read syllabus',
            'read timetable',
            'read notice',
            'check result',
        ]);

        //assign permissions to librarian

        //assign permissions to accountant
    }
}
