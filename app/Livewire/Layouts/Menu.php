<?php

namespace App\Livewire\Layouts;

use App\Models\Organization;
use Livewire\Component;

class Menu extends Component
{
    public $menu;

    public function mount()
    {
        $this->menu = [
            [
                'type' => 'menu-item',
                'icon' => 'gauge',
                'text' => 'Dashboard',
                'route' => 'dashboard',
            ],
            ['header' => 'Manage Profile'],
            [
                'type' => 'menu-item',
                'icon' => 'user',
                'text' => 'User Profile',
                'route' => 'profile.show',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'building-2',
                'text' => 'Organizations',
                'visible' => auth()->user()->can('viewAny', Organization::class),
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Organizations',
                        'route' => 'organizations.index',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Organization',
                        'route' => 'organizations.create',
                        'visible' => auth()->user()->can('create', Organization::class),
                    ],
                ],
            ],
            ['header' => 'Multi Schools Management'],
            [
                'type' => 'menu-item',
                'text' => 'Schools',
                'icon' => 'school',
                'submenu' => [[
                    'type' => 'menu-item',
                    'text' => 'View Schools',
                    'route' => 'schools.index',
                    'can' => 'read school',
                ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create School',
                        'route' => 'schools.create',
                        'can' => 'create school',
                    ],
                ],
            ],
            ['header' => 'Administration'],
            [
                'type' => 'menu-item',
                'icon' => 'settings',
                'text' => 'School Settings',
                'route' => 'schools.settings',
                'can' => 'manage school settings',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Classes',
                'icon' => 'presentation',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Classes',
                        'route' => 'classes.index',
                        'can' => 'read class',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Academic Levels',
                        'route' => 'academic-levels.index',
                        'can' => 'read class',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Add Academic Level',
                        'route' => 'academic-levels.create',
                        'can' => 'create class',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Class',
                        'route' => 'classes.create',
                        'can' => 'create class',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'View Class Groups',
                        'route' => 'class-groups.index',
                        'can' => 'read class group',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Class Group',
                        'route' => 'class-groups.create',
                        'can' => 'create class group',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Sections',
                'icon' => 'landmark',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View sections',
                        'route' => 'sections.index',
                        'can' => 'read section',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Cycle sections',
                        'route' => 'academic-cycle-sections.index',
                        'can' => 'read section',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Add cycle section',
                        'route' => 'academic-cycle-sections.create',
                        'can' => 'create section',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Roll sections forward',
                        'route' => 'academic-cycle-sections.roll-forward.show',
                        'can' => 'create section',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create section',
                        'route' => 'sections.create',
                        'can' => 'create section',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Students',
                'icon' => 'user',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View students',
                        'route' => 'students.index',
                        'can' => 'read student',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create student',
                        'route' => 'students.create',
                        'can' => 'create student',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Promote students',
                        'route' => 'students.promote',
                        'can' => 'promote student',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Manage promotions',
                        'route' => 'students.promotions',
                        'can' => 'read promotion',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Graduate students',
                        'route' => 'students.graduate',
                        'can' => 'graduate student',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Manage graduations',
                        'route' => 'students.graduations',
                        'can' => 'view graduations',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Teachers',
                'icon' => 'user',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View teachers',
                        'route' => 'teachers.index',
                        'can' => 'read teacher',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create teacher',
                        'route' => 'teachers.create',
                        'can' => 'create teacher',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Parents',
                'icon' => 'user',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View parents',
                        'route' => 'parents.index',
                        'can' => 'read parent',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create parent',
                        'route' => 'parents.create',
                        'can' => 'create parent',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Admins',
                'icon' => 'user',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View admins',
                        'route' => 'admins.index',
                        'can' => 'read admin',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create admin',
                        'route' => 'admins.create',
                        'can' => 'create admin',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Account invitations',
                        'route' => 'users.invitations.index',
                        'can' => 'manage account access',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Academic years',
                'icon' => 'calendar',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View academic years',
                        'route' => 'academic-years.index',
                        'can' => 'read academic year',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create academic year',
                        'route' => 'academic-years.create',
                        'can' => 'create academic year',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Academic Periods',
                'icon' => 'clock',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View academic periods',
                        'route' => 'academic-periods.index',
                        'can' => 'read academic period',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create academic period',
                        'route' => 'academic-periods.create',
                        'can' => 'create academic period',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Fees',
                'icon' => 'dollar-sign',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Fee Invoices',
                        'route' => 'fee-invoices.index',
                        'can' => 'read fee invoice',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Fee Invoice',
                        'route' => 'fee-invoices.create',
                        'can' => 'create fee invoice',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'View Fees',
                        'route' => 'fees.index',
                        'can' => 'read fee',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Fee',
                        'route' => 'fees.create',
                        'can' => 'create fee',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'View Fee Categories',
                        'route' => 'fee-categories.index',
                        'can' => 'read fee category',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Fee Category',
                        'route' => 'fee-categories.create',
                        'can' => 'create fee category',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Subjects',
                'icon' => 'lightbulb',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View subjects',
                        'route' => 'subjects.index',
                        'can' => 'read subject',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create subject',
                        'route' => 'subjects.create',
                        'can' => 'create subject',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Assign teacher to subjects',
                        'route' => 'subjects.assign-teacher',
                        'can' => 'update subject',
                    ],
                ],
            ],
            ['header' => 'Academics'],
            [
                'type' => 'menu-item',
                'text' => 'Notices',
                'icon' => 'bell',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View notices',
                        'route' => 'notices.index',
                        'can' => 'read notice',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create notice',
                        'route' => 'notices.create',
                        'can' => 'create notice',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Syllabi',
                'icon' => 'list',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Syllabi',
                        'route' => 'syllabi.index',
                        'can' => 'read syllabus',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Syllabus',
                        'route' => 'syllabi.create',
                        'can' => 'create syllabus',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Timetables',
                'icon' => 'list-checks',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Timetables',
                        'route' => 'timetables.index',
                        'can' => 'read timetable',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Timetable',
                        'route' => 'timetables.create',
                        'can' => 'create timetable',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'View custom items',
                        'route' => 'custom-timetable-items.index',
                        'can' => 'read custom timetable items',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Custom Items',
                        'route' => 'custom-timetable-items.create',
                        'can' => 'create custom timetable items',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Exams',
                'icon' => 'book-open',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Exams',
                        'route' => 'exams.index',
                        'can' => 'read exam',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Exam',
                        'route' => 'exams.create',
                        'can' => 'create exam',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Manage Exam records',
                        'route' => 'exam-records.index',
                        'can' => 'update exam record',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Exam tabulation sheet',
                        'route' => 'exams.tabulation',
                        'can' => 'read exam',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Academic Period Result Sheet',
                        'route' => 'exams.academic-period-result-tabulation',
                        'can' => 'read exam',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Academic Year Result Sheet',
                        'route' => 'exams.academic-year-result-tabulation',
                        'can' => 'read exam',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Result Checker',
                        'route' => 'exams.result-checker',
                        'can' => 'check result',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'Grade Systems',
                'icon' => 'graduation-cap',
                'submenu' => [
                    [
                        'type' => 'menu-item',
                        'text' => 'View Grading System',
                        'route' => 'grade-systems.index',
                        'can' => 'read grade system',
                    ],
                    [
                        'type' => 'menu-item',
                        'text' => 'Create Grades',
                        'route' => 'grade-systems.create',
                        'can' => 'create grade system',
                    ],
                ],
            ],
            [
                'type' => 'menu-item',
                'text' => 'View Logs',
                'route' => 'log-viewer.index',
                'icon' => 'sticky-note',
                // this menu item checks with roles for now so this prevents other non super users from viewing menu item
                'can' => 'view logs',
            ],
        ];

        $this->menu = $this->withVisibility($this->menu);
    }

    public function render()
    {
        return view('livewire.layouts.menu');
    }

    /**
     * Mark each item according to its own permission and its visible children.
     *
     * @param  list<array<string, mixed>>  $menu
     * @return list<array<string, mixed>>
     */
    private function withVisibility(array $menu): array
    {
        return array_map(function (array $menuItem): array {
            if (isset($menuItem['header'])) {
                return $menuItem;
            }

            if (isset($menuItem['submenu'])) {
                $menuItem['submenu'] = $this->withVisibility($menuItem['submenu']);
            }

            $hasVisibleSubmenu = !isset($menuItem['submenu'])
                || collect($menuItem['submenu'])->contains(
                    fn (array $submenu): bool => $submenu['visible'] ?? true,
                );

            $menuItem['visible'] = ($menuItem['visible'] ?? true)
                && (!isset($menuItem['can']) || auth()->user()->can($menuItem['can']))
                && $hasVisibleSubmenu;

            return $menuItem;
        }, $menu);
    }
}
