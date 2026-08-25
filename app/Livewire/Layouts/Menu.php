<?php

namespace App\Livewire\Layouts;

use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Models\CalendarEvent;
use App\Models\CampusMoveRequest;
use App\Models\DataSharingRequest;
use App\Models\Incident;
use App\Models\Organization;
use App\Models\School;
use App\Models\StaffLeaveRequest;
use App\Models\StaffProfile;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\SupportPlan;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use App\Services\School\SchoolService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Menu extends Component
{
    /**
     * @var Collection<int, School>
     */
    public Collection $schools;

    /**
     * @var list<array<string, mixed>>
     */
    public array $menu = [];

    public function mount(SchoolService $schoolService): void
    {
        $user = auth()->user();
        $this->schools = $schoolService->getSchoolsForUser($user);

        // A person can hold organization authority without a working school,
        // so the sidebar must render before any school is chosen.
        $organization = current_school()?->organization;
        $portalEnrollment = $this->portalEnrollment($user);
        $readsOwnRecords = $this->readsOwnRecords($user);
        $this->menu = [
            [
                'type' => 'menu-item',
                'icon' => 'gauge',
                'text' => 'Dashboard',
                'route' => 'dashboard',
            ],
            ...(!$readsOwnRecords && $portalEnrollment === null ? [] : [
                ['header' => 'My school'],
            ]),
            ...(!$readsOwnRecords ? [] : [
                [
                    'type' => 'menu-item',
                    'icon' => 'layout-list',
                    'text' => 'Everything of mine',
                    'route' => 'portal.overview',
                ],
                [
                    'type' => 'menu-item',
                    'icon' => 'bell',
                    'text' => 'Notification settings',
                    'route' => 'portal.notification-preferences.edit',
                ],
            ]),
            ...($portalEnrollment === null ? [] : [
                [
                    'type' => 'menu-item',
                    'icon' => 'calendar-days',
                    'text' => 'School calendar',
                    'route' => 'portal.calendar.index',
                    'parameters' => [$portalEnrollment],
                ],
            ]),
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
            ...($organization === null ? [] : [[
                'type' => 'menu-item',
                'icon' => 'calendar-range',
                'text' => 'Calendar templates',
                'route' => 'organizations.calendar-templates.index',
                'parameters' => [$organization],
                'visible' => $user->can('view', $organization),
            ]]),
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
                'icon' => 'presentation',
                'text' => school_terms('class_level', 'Class'),
                'route' => 'academic-levels.index',
                'can' => 'read class',
            ],
            [
                'type' => 'menu-item',
                'icon' => 'landmark',
                'text' => school_terms('section', 'Section'),
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
                'text' => 'Record sharing',
                'icon' => 'share-2',
                'route' => 'data-sharing-requests.index',
                'visible' => $user->can('viewAny', DataSharingRequest::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Campus moves',
                'icon' => 'building-2',
                'route' => 'campus-moves.index',
                'visible' => $user->can('viewAny', CampusMoveRequest::class),
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
                'text' => 'Groups',
                'icon' => 'users-round',
                'route' => 'cohorts.index',
                'can' => 'read cohort',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Graduation plans',
                'icon' => 'graduation-cap',
                'route' => 'graduation-plans.index',
                'can' => 'read graduation plan',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Programmes',
                'icon' => 'sparkles',
                'route' => 'programs.index',
                'can' => 'read program',
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
                'text' => school_terms('course', 'Course offering'),
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
                'text' => 'Rankings',
                'icon' => 'list-ordered',
                'route' => 'rankings.index',
                'visible' => feature_enabled(Feature::Ranking) && $user->can('read ranking'),
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
                'text' => 'Reports',
                'icon' => 'file-text',
                'route' => 'reports.index',
                'can' => 'read report',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Family requests',
                'icon' => 'message-square',
                'route' => 'portal-requests.index',
                'can' => 'read portal request',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Calendar',
                'icon' => 'calendar-days',
                'route' => 'calendar-events.index',
                'visible' => feature_enabled(Feature::Events) && $user->can('viewAny', CalendarEvent::class),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Notices',
                'icon' => 'bell',
                'route' => 'notices.index',
                'can' => 'read notice',
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
                'route' => 'fee-invoices.index',
                'can' => 'read fee invoice',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Boarding',
                'icon' => 'bed-double',
                'route' => 'dormitories.index',
                'visible' => feature_enabled(Feature::Boarding) && $user->can('read boarding'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Nights away',
                'icon' => 'moon',
                'route' => 'overnight-leaves.index',
                'visible' => feature_enabled(Feature::Boarding) && $user->can('read boarding'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Facilities',
                'icon' => 'building-2',
                'route' => 'facilities.index',
                'can' => 'read facility',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Library',
                'icon' => 'library',
                'route' => 'library-copies.index',
                'visible' => feature_enabled(Feature::Library) && $user->can('read library'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Lending desk',
                'icon' => 'book-open-check',
                'route' => 'library-loans.index',
                'visible' => feature_enabled(Feature::Library) && $user->can('read library'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Library queue',
                'icon' => 'clock',
                'route' => 'library-reservations.index',
                'visible' => feature_enabled(Feature::Library) && $user->can('read library'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Budgets',
                'icon' => 'wallet',
                'route' => 'budgets.index',
                'can' => 'read budget',
            ],
            [
                'type' => 'menu-item',
                'text' => 'Imports',
                'icon' => 'upload',
                'route' => 'imports.index',
                'visible' => feature_enabled(Feature::Imports) && $user->can('read import'),
            ],
            [
                'type' => 'menu-item',
                'text' => 'Roles',
                'icon' => 'shield-check',
                'route' => 'roles.index',
                'can' => 'read role',
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
     * Check whether this person has records of their own to read.
     *
     * A guardian of children at two campuses, and a learner enrolled at two,
     * both get one page that names every campus. Staff hold no enrollment, so
     * they never see it.
     */
    private function readsOwnRecords(User $user): bool
    {
        $access = app(PortalAccess::class);

        foreach ($access->enrollmentsFor($user) as $enrollment) {
            if ($access->isOpen($enrollment->school_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the enrollment whose portal this person reads.
     *
     * A staff member holds no enrollment and no children, so the family
     * entries stay hidden for them. A guardian of more than one child reads
     * the first, and every family screen names the child it is showing.
     */
    private function portalEnrollment(User $user): ?StudentRecord
    {
        $access = app(PortalAccess::class);

        /** @var StudentRecord|null $enrollment */
        $enrollment = $access->enrollmentsFor($user)->first();

        if ($enrollment === null) {
            return null;
        }

        // A family holds no working school, so both gates read the school the
        // child attends.
        $isOpen = $access->areaIsOpen(PortalArea::Calendar, $enrollment->school_id)
            && features()->enabled(Feature::Events, $enrollment->school_id);

        return $isOpen ? $enrollment : null;
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
