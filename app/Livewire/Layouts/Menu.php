<?php

namespace App\Livewire\Layouts;

use App\Enums\Feature;
use App\Models\CampusMoveRequest;
use App\Models\Incident;
use App\Models\Organization;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use App\Models\StudentHealthRecord;
use App\Models\SupportPlan;
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
        // A person can hold organization authority without a working school,
        // so the sidebar must render before any school is chosen.
        $organization = current_school()?->organization;
        $this->menu = [
            [
                'type'  => 'menu-item',
                'icon'  => 'gauge',
                'text'  => 'Dashboard',
                'route' => 'dashboard',
            ],
            ['header' => 'Organization'],
            [
                'type'    => 'menu-item',
                'icon'    => 'building-2',
                'text'    => 'Organizations',
                'route'   => 'organizations.index',
                'visible' => $user->can('viewAny', Organization::class),
            ],
            [
                'type'  => 'menu-item',
                'icon'  => 'school',
                'text'  => 'Schools',
                'route' => 'schools.index',
                'can'   => 'read school',
            ],
            ...($organization === null ? [] : [[
                'type'       => 'menu-item',
                'icon'       => 'calendar-range',
                'text'       => 'Calendar templates',
                'route'      => 'organizations.calendar-templates.index',
                'parameters' => [$organization],
                'visible'    => $user->can('view', $organization),
            ]]),
            ['header' => 'Setup'],
            [
                'type'  => 'menu-item',
                'icon'  => 'settings',
                'text'  => 'School settings',
                'route' => 'schools.settings',
                'can'   => 'manage school settings',
            ],
            [
                'type'  => 'menu-item',
                'icon'  => 'calendar',
                'text'  => 'Academic cycles',
                'route' => 'academic-years.index',
                'can'   => 'read academic year',
            ],
            [
                'type'  => 'menu-item',
                'icon'  => 'clock',
                'text'  => school_terms('period', 'Academic period'),
                'route' => 'academic-periods.index',
                'can'   => 'read academic period',
            ],
            [
                'type'  => 'menu-item',
                'icon'  => 'presentation',
                'text'  => school_terms('class_level', 'Academic level'),
                'route' => 'academic-levels.index',
                'can'   => 'read class',
            ],
            [
                'type'  => 'menu-item',
                'icon'  => 'landmark',
                'text'  => school_terms('section', 'Cycle section'),
                'route' => 'academic-cycle-sections.index',
                'can'   => 'read section',
            ],
            ['header' => 'People'],
            [
                'type'  => 'menu-item',
                'text'  => 'Students',
                'icon'  => 'user',
                'route' => 'students.index',
                'can'   => 'read student',
            ],
            [
                'type'    => 'menu-item',
                'text'    => 'Campus moves',
                'icon'    => 'building-2',
                'route'   => 'campus-moves.index',
                'visible' => $user->can('viewAny', CampusMoveRequest::class),
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Teachers',
                'icon'  => 'graduation-cap',
                'route' => 'teachers.index',
                'can'   => 'read teacher',
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Parents',
                'icon'  => 'users',
                'route' => 'parents.index',
                'can'   => 'read parent',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Groups',
                'icon' => 'users-round',
                'route' => 'cohorts.index',
                'can' => 'read cohort',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Programmes',
                'icon' => 'sparkles',
                'route' => 'programs.index',
                'can' => 'read cohort',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Administrators',
                'icon' => 'shield-check',
                'route' => 'admins.index',
                'can'   => 'read admin',
            ],
            ['header' => 'Teaching'],
            [
                'type'  => 'menu-item',
                'text'  => 'Subjects',
                'icon'  => 'lightbulb',
                'route' => 'subjects.index',
                'can'   => 'read subject',
            ],
            [
                'type'  => 'menu-item',
                'text'  => school_terms('course', 'Course offering'),
                'icon'  => 'book-marked',
                'route' => 'course-offerings.index',
                'can'   => 'read subject',
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Syllabi',
                'icon'  => 'list',
                'route' => 'syllabi.index',
                'can'   => 'read syllabus',
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Timetables',
                'icon'  => 'list-checks',
                'route' => 'timetables.index',
                'can'   => 'read timetable',
            ],
            ['header' => 'Assessment'],
            [
                'type'  => 'menu-item',
                'text'  => 'Gradebooks',
                'icon'  => 'clipboard-pen-line',
                'route' => 'course-offerings.index',
                'can'   => 'menu-gradebook',
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Exam schedules',
                'icon'  => 'calendar-days',
                'route' => 'exams.index',
                'can'   => 'read exam',
            ],
            [
                'type'  => 'menu-item',
                'text'  => 'Grading scales',
                'icon'  => 'graduation-cap',
                'route' => 'grading-scales.index',
                'can'   => 'manage grading scale',
            ],
            ['header' => 'Operations'],
            [
                'type'  => 'menu-item',
                'text'  => 'Notices',
                'icon'  => 'bell',
                'route' => 'notices.index',
                'can'   => 'read notice',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Cases',
                'icon' => 'shield-alert',
                'route' => 'incidents.index',
                'visible' => feature_enabled(Feature::Discipline) && $user->can('viewAny', Incident::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Support plans',
                'icon' => 'heart-handshake',
                'route' => 'support-plans.index',
                'visible' => feature_enabled(Feature::Wellbeing) && $user->can('viewAny', SupportPlan::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Health records',
                'icon' => 'heart-pulse',
                'route' => 'health-records.index',
                'visible' => feature_enabled(Feature::Wellbeing) && $user->can('viewAny', StudentHealthRecord::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Staff',
                'icon' => 'briefcase',
                'route' => 'staff-profiles.index',
                'visible' => feature_enabled(Feature::StaffOperations) && $user->can('viewAny', StaffProfile::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Staff leave',
                'icon' => 'plane',
                'route' => 'staff-leave.index',
                'visible' => feature_enabled(Feature::StaffOperations) && $user->can('viewAny', StaffLeaveRequest::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Finance',
                'icon' => 'dollar-sign',
>>>>>>> 6cd11324 (Add screens for the five unreachable domains)
                'route' => 'fee-invoices.index',
                'can'   => 'read fee invoice',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Imports',
                'icon' => 'upload',
                'route' => 'imports.index',
                'visible' => feature_enabled(Feature::Imports) && $user->can('read import'),
            ],
            ['header' => 'System'],
            [
                'type'  => 'menu-item',
                'text'  => 'Activity logs',
                'route' => 'log-viewer.index',
                'icon'  => 'sticky-note',
                'can'   => 'view logs',
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
     * @param list<array<string, mixed>> $menu
     *
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
