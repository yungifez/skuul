<?php

namespace App\Services\School;

use App\Enums\Role;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\GradingScale;
use App\Models\School;
use App\Models\User;
use App\Services\Curriculum\InstructionalModelResolver;

class SchoolSetupChecklist
{
    public function __construct(
        private InstructionalModelResolver $instructionalModels,
    ) {}

    /**
     * Build the setup checklist for the school and the currently selected year.
     *
     * @return array{
     *     academicYear: AcademicYear|null,
     *     counts: array{academicLevels: int, academicPeriods: int, cycleSections: int, courseOfferings: int, gradingScales: int, teachers: int, students: int},
     *     items: list<array{key: string, title: string, description: string, reason: string, complete: bool, required: bool, group: string, url: string, action: string}>,
     *     completed: int,
     *     total: int,
     *     required_remaining: int,
     *     next: array{key: string, title: string, description: string, reason: string, complete: bool, required: bool, group: string, url: string, action: string}|null
     * }
     */
    public function for(School $school): array
    {
        $academicYear = current_academic_year();
        $hasAcademicYear = AcademicYear::query()->inSchool($school)->exists();
        $academicLevels = AcademicLevel::query()->inSchool($school)->where('is_group', false)->count();
        $academicPeriods = $academicYear?->topLevelPeriods()->count() ?? 0;
        $cycleSections = $academicYear === null
            ? 0
            : AcademicCycleSection::query()
                ->inSchool($school)
                ->where('academic_year_id', $academicYear->id)
                ->count();
        $courseOfferings = $academicYear === null
            ? 0
            : CourseOffering::query()
                ->inSchool($school)
                ->where('academic_year_id', $academicYear->id)
                ->count();
        $gradingScales = GradingScale::query()->inSchool($school)->count();
        $teachers = User::query()->ofSchool($school)->role(Role::Teacher)->count();
        $students = User::query()->ofSchool($school)->students()->activeStudents()->count();
        $academicYearLabel = school_term('academic_year', 'School year');
        $academicYearLabelLower = strtolower($academicYearLabel);
        $classLabel = school_term('class_level', 'Class');
        $classLabelLower = strtolower($classLabel);
        $classesLabel = school_terms('class_level', 'class');
        $sectionsLabel = school_terms('section', 'section');

        $items = [
            $this->item(
                key: 'school_details',
                title: 'School details',
                description: 'Keep the school name and address ready for staff, families and printed records.',
                reason: filled($school->name) && filled($school->address) ? '' : 'Add the school address before using printed records and communications.',
                complete: filled($school->name) && filled($school->address),
                required: true,
                group: 'School basics',
                url: route('schools.edit', $school),
                action: 'Review school details',
            ),
            $this->item(
                key: 'academic_year',
                title: 'Current '.$academicYearLabelLower,
                description: 'Choose the '.$academicYearLabelLower.' that this request and the setup work belong to.',
                reason: $academicYear === null ? 'No current '.$academicYearLabelLower.' is selected.' : '',
                complete: $academicYear !== null,
                required: true,
                group: 'Prepare the year',
                url: $hasAcademicYear ? route('academic-years.index') : route('academic-years.create', ['setup' => 1]),
                action: $hasAcademicYear ? 'Manage '.$academicYearLabelLower.'s' : 'Create first '.$academicYearLabelLower,
            ),
            $this->item(
                key: 'academic_periods',
                title: ucfirst(school_terms('period', 'term')).' or reporting periods',
                description: 'Divide the '.$academicYearLabelLower.' into the terms, semesters or reporting periods your school uses.',
                reason: $academicYear === null
                    ? 'Choose a current '.$academicYearLabelLower.' first, then add its terms or reporting periods.'
                    : ($academicPeriods === 0 ? 'No terms or reporting periods have been added for the current '.$academicYearLabelLower.'.' : ''),
                complete: $academicYear !== null && $academicPeriods > 0,
                required: true,
                group: 'Prepare the year',
                url: $academicYear === null ? route('academic-years.index') : route('academic-years.show', $academicYear),
                action: $academicYear === null ? 'Choose school year' : 'Set up calendar',
            ),
            $this->item(
                key: 'instructional_model',
                title: 'Teaching approach',
                description: 'Tell Skuul whether learners stay together or move between subject classes.',
                reason: $academicYear === null
                    ? 'Choose a current school year first, then select the teaching approach for it.'
                    : ($this->instructionalModels->isChosen($academicYear, $school) ? '' : 'No teaching approach has been chosen for the current school year.'),
                complete: $academicYear !== null && $this->instructionalModels->isChosen($academicYear, $school),
                required: true,
                group: 'Prepare the year',
                url: $academicYear === null ? route('academic-years.index') : route('academic-years.instructional-model.edit', $academicYear),
                action: $academicYear === null ? 'Choose school year' : 'Set teaching approach',
            ),
            $this->item(
                key: 'academic_levels',
                title: ucfirst($classesLabel),
                description: 'Create the reusable grades or '.$classesLabel.' your school teaches.',
                reason: $academicLevels === 0 ? 'No '.$classesLabel.' have been added yet.' : '',
                complete: $academicLevels > 0,
                required: true,
                group: 'Build the teaching structure',
                url: route('academic-levels.index'),
                action: 'Manage '.$classesLabel,
            ),
            $this->item(
                key: 'cycle_sections',
                title: ucfirst($sectionsLabel).' for this year',
                description: 'Create the arms, homerooms or '.$sectionsLabel.' that run in the current '.$academicYearLabelLower.'.',
                reason: $academicYear === null
                    ? 'Choose a current '.$academicYearLabelLower.' first, then create its '.$sectionsLabel.'.'
                    : ($cycleSections === 0 ? 'No '.$sectionsLabel.' have been created for the current '.$academicYearLabelLower.'.' : ''),
                complete: $academicYear !== null && $cycleSections > 0,
                required: true,
                group: 'Build the teaching structure',
                url: route('academic-cycle-sections.index'),
                action: 'Manage '.$classesLabel,
            ),
            $this->item(
                key: 'course_offerings',
                title: 'Subjects being taught',
                description: 'Choose the subjects each grade or '.$classLabelLower.' will study and assign their teaching structure.',
                reason: $academicYear === null
                    ? 'Choose a current school year first, then set up the subjects being taught.'
                    : ($courseOfferings === 0 ? 'No subjects have been set up for the current school year.' : ''),
                complete: $academicYear !== null && $courseOfferings > 0,
                required: true,
                group: 'Build the teaching structure',
                url: route('course-offerings.index'),
                action: 'Manage subjects',
            ),
            $this->item(
                key: 'grading_scales',
                title: 'Grading scales',
                description: 'Give teachers the grade names and options they use when marking work.',
                reason: $gradingScales === 0 ? 'No grading scale has been configured yet.' : '',
                complete: $gradingScales > 0,
                required: false,
                group: 'Recommended next steps',
                url: route('grading-scales.index'),
                action: 'Manage grading scales',
            ),
            $this->item(
                key: 'staff_access',
                title: 'Staff access',
                description: 'Invite or create the staff accounts that need to work in this school.',
                reason: $teachers === 0 ? 'No teacher account has been added yet.' : '',
                complete: $teachers > 0,
                required: false,
                group: 'Recommended next steps',
                url: route('admins.index'),
                action: 'Manage staff access',
            ),
            $this->item(
                key: 'student_records',
                title: 'Student records',
                description: 'Add learners so classes, attendance, communication and reporting have people to work with.',
                reason: $students === 0 ? 'No active student record has been added yet.' : '',
                complete: $students > 0,
                required: false,
                group: 'Recommended next steps',
                url: route('students.index'),
                action: 'Manage students',
            ),
        ];

        $completed = count(array_filter($items, fn (array $item): bool => $item['complete']));
        $requiredRemaining = count(array_filter($items, fn (array $item): bool => $item['required'] && !$item['complete']));
        $next = null;

        foreach ($items as $item) {
            if (!$item['complete'] && $item['required']) {
                $next = $item;
                break;
            }
        }

        if ($next === null) {
            foreach ($items as $item) {
                if (!$item['complete']) {
                    $next = $item;
                    break;
                }
            }
        }

        return [
            'academicYear' => $academicYear,
            'counts' => [
                'academicLevels' => $academicLevels,
                'academicPeriods' => $academicPeriods,
                'cycleSections' => $cycleSections,
                'courseOfferings' => $courseOfferings,
                'gradingScales' => $gradingScales,
                'teachers' => $teachers,
                'students' => $students,
            ],
            'items' => $items,
            'completed' => $completed,
            'total' => count($items),
            'required_remaining' => $requiredRemaining,
            'next' => $next,
        ];
    }

    /**
     * @return array{key: string, title: string, description: string, reason: string, complete: bool, required: bool, group: string, url: string, action: string}
     */
    private function item(
        string $key,
        string $title,
        string $description,
        string $reason,
        bool $complete,
        bool $required,
        string $group,
        string $url,
        string $action,
    ): array {
        return compact('key', 'title', 'description', 'reason', 'complete', 'required', 'group', 'url', 'action');
    }
}
