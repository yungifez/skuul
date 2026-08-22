<?php

namespace App\Livewire\Layouts;

use App\Models\Organization;
use Livewire\Component;

class Menu extends Component
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $menu = [];

    public function mount(): void
    {
        $user = auth()->user();
        $organization = current_school()->organization;
        $this->menu = [
            [
                'type' => 'menu-item',
                'icon' => 'gauge',
                'text' => 'Dashboard',
                'route' => 'dashboard',
            ],
            ['header' => 'Organization'],
            [
                'type' => 'menu-item',
                'icon' => 'building-2',
                'text' => 'Organizations',
                'route' => 'organizations.index',
                'visible' => $user->can('viewAny', Organization::class),
            ],
            [
                'type' => 'menu-item',
                'icon' => 'school',
                'text' => 'Schools',
                'route' => 'schools.index',
                'can' => 'read school',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'calendar-range',
                'text' => 'Calendar templates',
                'route' => 'organizations.calendar-templates.index',
                'parameters' => [$organization],
                'visible' => $user->can('view', $organization),
            ],
            ['header' => 'Setup'],
            [
                'type' => 'menu-item',
                'icon' => 'settings',
                'text' => 'School settings',
                'route' => 'schools.settings',
                'can' => 'manage school settings',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'calendar',
                'text' => 'Academic cycles',
                'route' => 'academic-years.index',
                'can' => 'read academic year',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'clock',
                'text' => school_terms('period', 'Academic period'),
                'route' => 'academic-periods.index',
                'can' => 'read academic period',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'presentation',
                'text' => school_terms('class_level', 'Academic level'),
                'route' => 'academic-levels.index',
                'can' => 'read class',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'landmark',
                'text' => school_terms('section', 'Class').' this year',
                'route' => 'academic-cycle-sections.index',
                'can' => 'read section',
            ],
            ['header' => 'People'],
            [
                'type' => 'menu-item',
                'text' => 'Students',
                'icon' => 'user',
                'route' => 'students.index',
                'can' => 'read student',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Teachers',
                'icon' => 'graduation-cap',
                'route' => 'teachers.index',
                'can' => 'read teacher',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Parents',
                'icon' => 'users',
                'route' => 'parents.index',
                'can' => 'read parent',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Administrators',
                'icon' => 'shield-check',
                'route' => 'admins.index',
                'can' => 'read admin',
            ],
            ['header' => 'Teaching'],
            [
                'type' => 'menu-item',
                'text' => 'Subjects',
                'icon' => 'lightbulb',
                'route' => 'subjects.index',
                'can' => 'read subject',
            ],
            [
                'type' => 'menu-item',
                'text' => school_terms('course', 'Course').' being taught',
                'icon' => 'book-marked',
                'route' => 'course-offerings.index',
                'can' => 'read subject',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Syllabi',
                'icon' => 'list',
                'route' => 'syllabi.index',
                'can' => 'read syllabus',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Timetables',
                'icon' => 'list-checks',
                'route' => 'timetables.index',
                'can' => 'read timetable',
            ],
            ['header' => 'Assessment'],
            [
                'type' => 'menu-item',
                'text' => 'Gradebooks',
                'icon' => 'clipboard-pen-line',
                'route' => 'course-offerings.index',
                'can' => 'menu-gradebook',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Exam schedules',
                'icon' => 'calendar-days',
                'route' => 'exams.index',
                'can' => 'read exam',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Grading scales',
                'icon' => 'graduation-cap',
                'route' => 'grading-scales.index',
                'can' => 'manage grading scale',
            ],
            ['header' => 'Operations'],
            [
                'type' => 'menu-item',
                'text' => 'Notices',
                'icon' => 'bell',
                'route' => 'notices.index',
                'can' => 'read notice',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Finance',
                'icon' => 'dollar-sign',
                'route' => 'fee-invoices.index',
                'can' => 'read fee invoice',
            ],
            ['header' => 'System'],
            [
                'type' => 'menu-item',
                'text' => 'Activity logs',
                'route' => 'log-viewer.index',
                'icon' => 'sticky-note',
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
