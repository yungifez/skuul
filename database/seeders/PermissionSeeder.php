<?php

namespace Database\Seeders;

use App\Enums\OrganizationPermission;
use App\Enums\PlatformPermission;
use App\Enums\Role as RoleName;
use Illuminate\Database\Seeder;
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
        /**
         * Create all permissions.
         *
         * EVERYTHING HERE IS USED IN A SINGULAR SENSE
         */
        foreach ([
            ...OrganizationPermission::all(),
            PlatformPermission::AccessAllSchools,
            PlatformPermission::AccessAllOrganizations,
            PlatformPermission::ManagePlatform,
        ] as $permission) {
            Permission::findOrCreate($permission);
        }

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
        Permission::firstOrCreate([
            'name' => 'migrate instructional model',
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

        // Permission for students
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

        // Permission for admins
        Permission::firstOrCreate([
            'name' => 'create admin',
        ]);
        Permission::firstOrCreate([
            'name' => 'read admin',
        ]);
        Permission::firstOrCreate([
            'name' => 'update admin',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete admin',
        ]);

        // Permission for academic year
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
        Permission::firstOrCreate([
            'name' => 'close academic period',
        ]);
        Permission::firstOrCreate([
            'name' => 'reopen academic period',
        ]);

        // Permission for teacher
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

        // Permission for parent
        Permission::firstOrCreate([
            'name' => 'create parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'read parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'update parent',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete parent',
        ]);

        // Permission for subject
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

        // Permission for student promotions
        Permission::firstOrCreate([
            'name' => 'promote student',
        ]);
        Permission::firstOrCreate([
            'name' => 'read promotion',
        ]);
        Permission::firstOrCreate([
            'name' => 'reset promotion',
        ]);

        // Permissions for moving a student to another campus. A campus
        // administrator asks; the receiving campus decides.
        Permission::firstOrCreate([
            'name' => 'request campus move',
        ]);
        Permission::firstOrCreate([
            'name' => 'approve campus move',
        ]);

        // permission for graduation
        Permission::firstOrCreate([
            'name' => 'graduate student',
        ]);
        Permission::firstOrCreate([
            'name' => 'view graduations',
        ]);
        Permission::firstOrCreate([
            'name' => 'reset graduation',
        ]);

        // permission for academic periods
        Permission::firstOrCreate([
            'name' => 'create academic period',
        ]);
        Permission::firstOrCreate([
            'name' => 'read academic period',
        ]);
        Permission::firstOrCreate([
            'name' => 'update academic period',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete academic period',
        ]);

        Permission::firstOrCreate([
            'name' => 'set academic period',
        ]);

        // permission for syllabus
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

        // permission for timetable
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

        // custom timetable item
        Permission::firstOrCreate([
            'name' => 'create custom timetable item',
        ]);
        Permission::firstOrCreate([
            'name' => 'read custom timetable item',
        ]);
        Permission::firstOrCreate([
            'name' => 'update custom timetable item',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete custom timetable item',
        ]);

        // exam permissions
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

        // Grading scales are reusable school configuration, not class-specific ranges.
        Permission::firstOrCreate([
            'name' => 'manage grading scale',
        ]);

        // permission for exam slots
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

        // Gradebook permissions
        Permission::firstOrCreate([
            'name' => 'read gradebook',
        ]);
        Permission::firstOrCreate([
            'name' => 'manage gradebook',
        ]);
        Permission::firstOrCreate([
            'name' => 'publish result',
        ]);

        Permission::firstOrCreate([
            'name' => 'read attendance',
        ]);
        Permission::firstOrCreate([
            'name' => 'take attendance',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-attendance',
        ]);

        // permission for notices

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

        // permission for account access
        Permission::firstOrCreate([
            'name' => 'manage account access',
        ]);

        // Permissions for discipline and safeguarding
        Permission::firstOrCreate([
            'name' => 'create incident',
        ]);
        Permission::firstOrCreate([
            'name' => 'read incident',
        ]);
        Permission::firstOrCreate([
            'name' => 'update incident',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete incident',
        ]);
        Permission::firstOrCreate([
            'name' => 'read safeguarding case',
        ]);

        // Permissions for student support and wellbeing
        Permission::firstOrCreate([
            'name' => 'create support plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'read support plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'update support plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete support plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'read confidential support plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'read health record',
        ]);
        Permission::firstOrCreate([
            'name' => 'update health record',
        ]);

        // Permissions for staff operations
        Permission::firstOrCreate([
            'name' => 'create staff profile',
        ]);
        Permission::firstOrCreate([
            'name' => 'read staff profile',
        ]);
        Permission::firstOrCreate([
            'name' => 'update staff profile',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete staff profile',
        ]);
        Permission::firstOrCreate([
            'name' => 'read staff leave',
        ]);
        Permission::firstOrCreate([
            'name' => 'request staff leave',
        ]);
        Permission::firstOrCreate([
            'name' => 'approve staff leave',
        ]);

        // Permissions for imports
        Permission::firstOrCreate([
            'name' => 'create import',
        ]);
        Permission::firstOrCreate([
            'name' => 'read import',
        ]);
        Permission::firstOrCreate([
            'name' => 'apply import',
        ]);

        // Permissions for cohorts, programmes, and graduation planning
        Permission::firstOrCreate([
            'name' => 'create cohort',
        ]);
        Permission::firstOrCreate([
            'name' => 'read cohort',
        ]);
        Permission::firstOrCreate([
            'name' => 'update cohort',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete cohort',
        ]);
        Permission::firstOrCreate([
            'name' => 'read restricted cohort',
        ]);
        Permission::firstOrCreate([
            'name' => 'create program',
        ]);
        Permission::firstOrCreate([
            'name' => 'read program',
        ]);
        Permission::firstOrCreate([
            'name' => 'update program',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete program',
        ]);
        Permission::firstOrCreate([
            'name' => 'manage graduation plan',
        ]);
        Permission::firstOrCreate([
            'name' => 'read graduation plan',
        ]);

        // Permission for rankings
        Permission::firstOrCreate([
            'name' => 'read ranking',
        ]);

        // Permissions for the school calendar
        Permission::firstOrCreate([
            'name' => 'create calendar event',
        ]);
        Permission::firstOrCreate([
            'name' => 'read calendar event',
        ]);
        Permission::firstOrCreate([
            'name' => 'update calendar event',
        ]);
        Permission::firstOrCreate([
            'name' => 'delete calendar event',
        ]);
        Permission::firstOrCreate([
            'name' => 'publish calendar event',
        ]);

        // Permissions for the student and guardian portal
        Permission::firstOrCreate([
            'name' => 'read portal request',
        ]);
        Permission::firstOrCreate([
            'name' => 'answer portal request',
        ]);

        // Permissions for sharing records between schools
        Permission::firstOrCreate([
            'name' => 'request data sharing',
        ]);
        Permission::firstOrCreate([
            'name' => 'approve data sharing',
        ]);
        Permission::firstOrCreate([
            'name' => 'fulfil data sharing',
        ]);

        // Permissions for reports
        Permission::firstOrCreate([
            'name' => 'create report',
        ]);
        Permission::firstOrCreate([
            'name' => 'read report',
        ]);

        // permissions for fee categories

        Permission::firstOrCreate([
            'name' => 'create fee category',
        ]);

        Permission::firstOrCreate([
            'name' => 'read fee category',
        ]);

        Permission::firstOrCreate([
            'name' => 'update fee category',
        ]);

        Permission::firstOrCreate([
            'name' => 'delete fee category',
        ]);

        // permissions for fees

        Permission::firstOrCreate([
            'name' => 'create fee',
        ]);

        Permission::firstOrCreate([
            'name' => 'read fee',
        ]);

        Permission::firstOrCreate([
            'name' => 'update fee',
        ]);

        Permission::firstOrCreate([
            'name' => 'delete fee',
        ]);

        // permissions for fee invoices

        Permission::firstOrCreate([
            'name' => 'create fee invoice',
        ]);

        Permission::firstOrCreate([
            'name' => 'read fee invoice',
        ]);

        Permission::firstOrCreate([
            'name' => 'update fee invoice',
        ]);

        Permission::firstOrCreate([
            'name' => 'delete fee invoice',
        ]);

        // fee invoice record

        Permission::firstOrCreate([
            'name' => 'create fee invoice record',
        ]);

        Permission::firstOrCreate([
            'name' => 'read fee invoice record',
        ]);

        Permission::firstOrCreate([
            'name' => 'update fee invoice record',
        ]);

        Permission::firstOrCreate([
            'name' => 'delete fee invoice record',
        ]);

        // budgets
        Permission::firstOrCreate([
            'name' => 'read budget',
        ]);

        Permission::firstOrCreate([
            'name' => 'manage budget',
        ]);

        // student money that leaves the school again
        Permission::firstOrCreate([
            'name' => 'refund student payment',
        ]);

        // header permissions (for controlling the menu headers)
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
            'name' => 'menu-academic-period',
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
            'name' => 'menu-gradebook',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-notice',
        ]);
        Permission::firstOrCreate([
            'name' => 'menu-fee',
        ]);
        /**
         * assign permissions to roles.
         */

        // assign permissions to admin
        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions([
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
            'menu-academic-period',
            'menu-exam',
            'menu-gradebook',
            'menu-notice',
            'menu-parent',
            'menu-fee',
            'manage school settings',
            'migrate instructional model',
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
            'close academic period',
            'reopen academic period',
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
            'request campus move',
            'approve campus move',
            'graduate student',
            'view graduations',
            'reset graduation',
            'create academic period',
            'read academic period',
            'update academic period',
            'delete academic period',
            'set academic period',
            'create syllabus',
            'read syllabus',
            'update syllabus',
            'delete syllabus',
            'create timetable',
            'read timetable',
            'update timetable',
            'delete timetable',
            'create custom timetable item',
            'read custom timetable item',
            'update custom timetable item',
            'delete custom timetable item',
            'create exam',
            'read exam',
            'update exam',
            'delete exam',
            'manage grading scale',
            'create exam slot',
            'read exam slot',
            'update exam slot',
            'delete exam slot',
            'read gradebook',
            'manage gradebook',
            'publish result',
            'read attendance',
            'take attendance',
            'menu-attendance',
            'create notice',
            'read notice',
            'update notice',
            'delete notice',
            'create parent',
            'read parent',
            'update parent',
            'delete parent',
            'manage account access',
            'create report',
            'read report',
            'create incident',
            'read incident',
            'update incident',
            'delete incident',
            'create support plan',
            'read support plan',
            'update support plan',
            'delete support plan',
            'create staff profile',
            'read staff profile',
            'update staff profile',
            'delete staff profile',
            'read staff leave',
            'request staff leave',
            'approve staff leave',
            'create import',
            'read import',
            'apply import',
            'read ranking',
            'create calendar event',
            'read calendar event',
            'update calendar event',
            'delete calendar event',
            'publish calendar event',
            'create cohort',
            'read cohort',
            'update cohort',
            'delete cohort',
            'create program',
            'read program',
            'update program',
            'delete program',
            'manage graduation plan',
            'read graduation plan',
            'read portal request',
            'answer portal request',
            'create custom timetable item',
            'read custom timetable item',
            'update custom timetable item',
            'delete custom timetable item',
            'create fee',
            'read fee',
            'update fee',
            'delete fee',
            'create fee category',
            'read fee category',
            'update fee category',
            'delete fee category',
            'create fee invoice',
            'read fee invoice',
            'update fee invoice',
            'delete fee invoice',
            'create fee invoice record',
            'read fee invoice record',
            'update fee invoice record',
            'delete fee invoice record',
            'refund student payment',
            'read budget',
            'manage budget',
        ]);

        // assign permissions to teacher
        $teacher = Role::where('name', 'teacher')->first();
        $teacher->syncPermissions([
            'header-academics',
            'header-administrate',
            'menu-syllabus',
            'menu-timetable',
            'menu-exam',
            'menu-gradebook',
            'menu-notice',
            'menu-student',
            'read student',
            'read exam',
            'read exam slot',
            'create syllabus',
            'read syllabus',
            'update syllabus',
            'delete syllabus',
            'create timetable',
            'read timetable',
            'update timetable',
            'delete timetable',
            'read gradebook',
            'manage gradebook',
            'read attendance',
            'take attendance',
            'menu-attendance',
            'read notice',
            'read calendar event',
            'read ranking',
        ]);

        // assign permissions to student
        $student = Role::where('name', 'student')->first();
        $student->syncPermissions([
            'header-academics',
            'header-administrate',
            'menu-fee',
            'menu-syllabus',
            'menu-timetable',
            'menu-notice',
            'menu-exam',
            'read syllabus',
            'read timetable',
            'read notice',
            'read fee invoice',
            'read calendar event',
        ]);

        // assign permissions to parent
        $parent = Role::where('name', 'parent')->first();
        $parent->syncPermissions([
            'header-academics',
            'header-administrate',
            'menu-syllabus',
            'menu-timetable',
            'menu-notice',
            'menu-exam',
            'menu-fee',
            'menu-student',
            'read student',
            'read syllabus',
            'read timetable',
            'read notice',
            'read fee invoice',
            'read calendar event',
        ]);

        $organizationAdmin = Role::query()
            ->where('name', RoleName::OrganizationAdmin)
            ->whereNull('school_id')
            ->firstOrFail();
        $organizationAdmin->syncPermissions(OrganizationPermission::all());

        $platformAdmin = Role::query()
            ->where('name', RoleName::PlatformAdmin)
            ->whereNull('school_id')
            ->firstOrFail();
        $platformAdmin->syncPermissions(Permission::query()->pluck('name')->all());

        // assign permissions to librarian

        // assign permissions to accountant
    }
}
