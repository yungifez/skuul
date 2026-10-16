<?php

namespace Database\Seeders\Demo;

use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;

/**
 * What the demo school seeder built, handed to each part that adds to it.
 */
class DemoSchool
{
    public Organization $organization;

    /** The campus the demo accounts work in. */
    public School $campus;

    /** A sister campus, so moves and record sharing have somewhere to go. */
    public School $sisterCampus;

    /** The district administrator who installed Skuul. */
    public User $platformAdmin;

    /** The principal, who runs the main campus. */
    public User $schoolAdmin;

    public User $accountant;

    public User $librarian;

    public AcademicYear $academicYear;

    /** @var array<string, AcademicPeriod> by name */
    public array $periods = [];

    /** The period that runs today. */
    public AcademicPeriod $currentPeriod;

    /** @var array<string, AcademicLevel> by grade name */
    public array $levels = [];

    /** @var array<string, AcademicCycleSection> by section name, such as "9A" */
    public array $sections = [];

    /** @var array<string, Subject> by name */
    public array $subjects = [];

    /** @var array<string, User> by subject name */
    public array $teachers = [];

    /** @var array<string, list<User>> learners by section name */
    public array $students = [];

    /** @var list<User> */
    public array $guardians = [];

    /** @var list<CourseOffering> */
    public array $offerings = [];

    /** The learner the demo student account signs in as. */
    public User $demoStudent;

    /** The guardian the demo parent account signs in as. */
    public User $demoParent;

    /**
     * Every learner, in a stable order.
     *
     * @return list<User>
     */
    public function allStudents(): array
    {
        return array_merge(...array_values($this->students));
    }
}
