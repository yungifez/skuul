<?php

namespace App\Http\Livewire\Layouts;

use Livewire\Component;

class Menu extends Component
{
    public $menu;

    public function mount()
    {
        $this->menu = [
            [
                'type'  => 'menu-item',
                'icon'  => 'fas fa-tachometer-alt',
                'text'  => 'Dashboard',
                'route' => 'dashboard',
            ],
            ['header' => 'Manage Profile'],
            [
                'type'  => 'menu-item',
                'icon'  => 'fas fa-user',
                'text'  => 'User Profile',
                'route' => 'profile.show',
            ],
            ['header' => 'Multi Schools Management', 'can' => 'header-schools'],
            [
                'type' => 'menu-item',
                'text' => 'Schools',
                'icon' => 'fas fa-school',
                'can'  => 'menu-school',

                'submenu' => [[
                    'type'  => 'menu-item',
                    'text'  => 'View Schools',
                    'route' => 'schools.index',
                    'can'   => 'read school',
                ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create School',
                        'route' => 'schools.create',
                        'can'   => 'create school',
                    ],
                ],
            ],
            ['header' => 'Administration', 'can' => 'header-administrate'],
            [
                'type'  => 'menu-item',
                'icon'  => 'fas fa-cog',
                'text'  => 'School Settings',
                'route' => 'schools.settings',
                'can'   => 'manage school settings',
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Classes',
                'icon'    => 'fas fa-chalkboard',
                'can'     => 'menu-class',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Classes',
                        'route' => 'classes.index',
                        'can'   => 'read class',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Class',
                        'route' => 'classes.create',
                        'can'   => 'create class',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Class Groups',
                        'route' => 'class-groups.index',
                        'can'   => 'read class group',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Class Group',
                        'route' => 'class-groups.create',
                        'can'   => 'create class group',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Sections',
                'icon'    => 'fas fa-landmark',
                'can'     => 'menu-section',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View sections',
                        'route' => 'sections.index',
                        'can'   => 'read section',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create section',
                        'route' => 'sections.create',
                        'can'   => 'create section',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Students',
                'icon'    => 'fas fa-user',
                'can'     => 'menu-student',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View students',
                        'route' => 'students.index',
                        'can'   => 'read student',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create student',
                        'route' => 'students.create',
                        'can'   => 'create student',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Promote students',
                        'route' => 'students.promote',
                        'can'   => 'promote student',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Manage promotions',
                        'route' => 'students.promotions',
                        'can'   => 'read promotion',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Graduate students',
                        'route' => 'students.graduate',
                        'can'   => 'graduate student',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Manage graduations',
                        'route' => 'students.graduations',
                        'can'   => 'view graduations',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Account Applications',
                'icon'    => 'fas fa-plus',
                'can'     => 'menu-account-application',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View account applications',
                        'route' => 'account-applications.index',
                        'can'   => 'read applicant',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View rejected applications',
                        'route' => 'account-applications.rejected-applications',
                        'can'   => 'read applicant',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Teachers',
                'icon'    => 'fas fa-user',
                'can'     => 'menu-teacher',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View teachers',
                        'route' => 'teachers.index',
                        'can'   => 'read teacher',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create teacher',
                        'route' => 'teachers.create',
                        'can'   => 'create teacher',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Parents',
                'icon'    => 'fas fa-user',
                'can'     => 'menu-parent',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View parents',
                        'route' => 'parents.index',
                        'can'   => 'read parent',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create parent',
                        'route' => 'parents.create',
                        'can'   => 'create parent',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Admins',
                'icon'    => 'fas fa-user',
                'can'     => 'menu-admin',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View admins',
                        'route' => 'admins.index',
                        'can'   => 'read admin',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create admin',
                        'route' => 'admins.create',
                        'can'   => 'create admin',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Academic years',
                'icon'    => 'fas fa-calendar',
                'can'     => 'menu-academic-year',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View academic years',
                        'route' => 'academic-years.index',
                        'can'   => 'read academic year',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create academic year',
                        'route' => 'academic-years.create',
                        'can'   => 'create academic year',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Semesters',
                'icon'    => 'fas fa-clock',
                'can'     => 'menu-semester',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View semesters',
                        'route' => 'semesters.index',
                        'can'   => 'read semester',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create semester',
                        'route' => 'semesters.create',
                        'can'   => 'create semester',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Fees',
                'icon'    => 'fas fa-dollar',
                'can'     => 'menu-fee',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Fee Invoices',
                        'route' => 'fee-invoices.index',
                        'can'   => 'read fee invoice',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Fee Invoice',
                        'route' => 'fee-invoices.create',
                        'can'   => 'create fee invoice',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Fees',
                        'route' => 'fees.index',
                        'can'   => 'read fee',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Fee',
                        'route' => 'fees.create',
                        'can'   => 'create fee',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Fee Categories',
                        'route' => 'fee-categories.index',
                        'can'   => 'read fee category',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Fee Category',
                        'route' => 'fee-categories.create',
                        'can'   => 'create fee category',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Subjects',
                'icon'    => 'fas fa-lightbulb',
                'can'     => 'menu-subject',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View subjects',
                        'route' => 'subjects.index',
                        'can'   => 'read subject',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create subject',
                        'route' => 'subjects.create',
                        'can'   => 'create subject',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Assign teacher to subjects',
                        'route' => 'subjects.assign-teacher',
                        'can'   => 'update subject',
                    ],
                ],
            ],
            ['header' => 'Academics', 'can' => 'header-academics'],
            [
                'type'    => 'menu-item',
                'text'    => 'Notices',
                'icon'    => 'fas fa-bell',
                'can'     => 'menu-notice',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View notices',
                        'route' => 'notices.index',
                        'can'   => 'read notice',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create notice',
                        'route' => 'notices.create',
                        'can'   => 'create notice',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Syllabi',
                'icon'    => 'fas fa-list-alt',
                'can'     => 'menu-syllabus',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Syllabi',
                        'route' => 'syllabi.index',
                        'can'   => 'read syllabus',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Syllabus',
                        'route' => 'syllabi.create',
                        'can'   => 'create syllabus',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Timetables',
                'icon'    => 'fas fa-tasks',
                'can'     => 'menu-timetable',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Timetables',
                        'route' => 'timetables.index',
                        'can'   => 'read timetable',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Timetable',
                        'route' => 'timetables.create',
                        'can'   => 'create timetable',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View custom items',
                        'route' => 'custom-timetable-items.index',
                        'can'   => 'read custom timetable items',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Custom Items',
                        'route' => 'custom-timetable-items.create',
                        'can'   => 'create custom timetable items',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Exams',
                'icon'    => 'fas fa-book-open',
                'can'     => 'menu-exam',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Exams',
                        'route' => 'exams.index',
                        'can'   => 'read exam',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Exam',
                        'route' => 'exams.create',
                        'can'   => 'create exam',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Manage Exam records',
                        'route' => 'exam-records.index',
                        'can'   => 'update exam record',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Exam tabulation sheet',
                        'route' => 'exams.tabulation',
                        'can'   => 'read exam',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Semester Result Sheet',
                        'route' => 'exams.semester-result-tabulation',
                        'can'   => 'read exam',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Academic Year Result Sheet',
                        'route' => 'exams.academic-year-result-tabulation',
                        'can'   => 'read exam',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Result Checker',
                        'route' => 'exams.result-checker',
                        'can'   => 'check result',
                    ],
                ],
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Grade Systems',
                'icon'    => 'fa fa-graduation-cap',
                'can'     => 'menu-grade-system',
                'submenu' => [
                    [
                        'type'  => 'menu-item',
                        'text'  => 'View Grading System',
                        'route' => 'grade-systems.index',
                        'can'   => 'read grade system',
                    ],
                    [
                        'type'  => 'menu-item',
                        'text'  => 'Create Grades',
                        'route' => 'grade-systems.create',
                        'can'   => 'create grade system',
                    ],
                ],
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'View Logs',
                'route' => 'blv.index',
                'icon'  => 'fa fa-sticky-note',
                //this menu item checks with roles for now so this prevents other non super users from viewing menu item
                'can' => 'view logs',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.layouts.menu');
    }
}
