<?php

namespace Database\Seeders;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Actions\Academic\SaveAcademicCalendar;
use App\Actions\Authorization\GrantSystemRole;
use App\Actions\Curriculum\AssignTeacher;
use App\Actions\Curriculum\ChangeAcademicCycleSectionStatus;
use App\Actions\Curriculum\ChangeCourseOfferingStatus;
use App\Actions\Curriculum\CreateAcademicCycleSection;
use App\Actions\Curriculum\CreateAcademicLevel;
use App\Actions\Curriculum\CreateCourseOfferingsForSections;
use App\Actions\Identity\ChangeGuardianLink;
use App\Actions\Organization\CreateOrganization;
use App\Actions\School\GrantSchoolMembership;
use App\Enums\AcademicPeriodType;
use App\Enums\AcademicStructureStatus;
use App\Enums\AccountStatus;
use App\Enums\CourseOfferingStatus;
use App\Enums\Role;
use App\Models\AcademicPeriod;
use App\Models\Installation;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\Subject;
use App\Models\User;
use App\Services\AcademicYear\AcademicYearService;
use App\Services\Student\StudentService;
use Database\Seeders\Demo\Administration;
use Database\Seeders\Demo\DemoPart;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\Demo\Finance;
use Database\Seeders\Demo\StudentLife;
use Database\Seeders\Demo\TeachingRecords;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Build the public demo: a small US school district with readable people,
 * classes and records on every screen.
 *
 * The test seeders fill the database with random values that suit tests but
 * read badly in a demo. This seeder writes a fixed, believable school through
 * the same actions the screens use, so every record keeps the rules the
 * application enforces. Dates follow today, so the demo never goes stale.
 */
class DemoSchoolSeeder extends Seeder
{
    public const DOMAIN = 'riversideusd.example';

    /** @var array<string, string> subject => teacher name */
    private const SUBJECTS = [
        'English Language Arts' => 'Sarah Mitchell',
        'Algebra' => 'Robert Kim',
        'Biology' => 'Angela Torres',
        'U.S. History' => 'Michael Johnson',
        'Spanish' => 'Lucia Hernandez',
        'Visual Arts' => 'Hannah Lee',
        'Physical Education' => 'Marcus Green',
    ];

    /** @var list<string> */
    private const GRADES = ['Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    /** @var list<string> */
    private const FIRST_NAMES = [
        'Olivia', 'Liam', 'Emma', 'Noah', 'Ava', 'Elijah', 'Sophia', 'Mason', 'Isabella', 'Lucas',
        'Mia', 'Logan', 'Amelia', 'Aiden', 'Harper', 'Jackson', 'Evelyn', 'Carter', 'Abigail', 'Owen',
        'Emily', 'Wyatt', 'Ella', 'Caleb', 'Avery', 'Nathan', 'Scarlett', 'Isaac', 'Grace', 'Dylan',
        'Chloe', 'Gavin', 'Zoey', 'Julian', 'Lily', 'Adrian', 'Nora', 'Miles', 'Riley', 'Jordan',
    ];

    /** @var list<string> */
    private const LAST_NAMES = [
        'Anderson', 'Baker', 'Campbell', 'Diaz', 'Edwards', 'Foster', 'Garcia', 'Hughes', 'Ingram', 'Jenkins',
        'Kelly', 'Lopez', 'Morgan', 'Nelson', 'Ortiz', 'Parker', 'Quinn', 'Reed', 'Sanders', 'Turner',
        'Underwood', 'Vasquez', 'Walker', 'Young', 'Zimmerman', 'Bennett', 'Coleman', 'Dawson', 'Ellis', 'Fleming',
        'Gibson', 'Harris', 'Jacobs', 'Kramer', 'Lawson', 'Myers', 'Nguyen', 'Owens', 'Price', 'Russell',
    ];

    /**
     * The parts that fill each area of the school, in order. Each part may
     * rely on the ones before it.
     *
     * @var list<class-string<DemoPart>>
     */
    private const PARTS = [
        TeachingRecords::class,
        StudentLife::class,
        Finance::class,
        Administration::class,
    ];

    private DemoSchool $demo;

    private string $password;

    public function run(): void
    {
        // A demo never writes to a real inbox, whatever the mail settings say.
        Mail::fake();
        Notification::fake();

        $this->password = Hash::make((string) config('demo.password'));
        $this->demo = new DemoSchool;

        $this->createDistrict();
        $this->createCalendar();
        $this->createStaff();
        $this->createStructure();
        $this->createStudents();
        $this->createOfferings();

        foreach (self::PARTS as $part) {
            app($part)->seed($this->demo);
        }
    }

    /**
     * The school this seeder built, for the parts that add to it.
     */
    public function demoSchool(): DemoSchool
    {
        return $this->demo;
    }

    private function createDistrict(): void
    {
        $this->demo->platformAdmin = $this->person('Megan Carter', 'megan.carter');
        app(GrantSystemRole::class)->grant($this->demo->platformAdmin, Role::PlatformAdmin);
        Auth::setUser($this->demo->platformAdmin);

        $this->demo->organization = app(CreateOrganization::class)->create([
            'name' => 'Riverside Unified School District',
        ], $this->demo->platformAdmin);

        $this->demo->campus = $this->campus('Riverside High School', 'RHS', '1200 River Road', '97214');
        $this->demo->sisterCampus = $this->campus('Maple Grove Middle School', 'MGM', '1450 Oak Street', '97205');

        app(GrantSchoolMembership::class)->grant($this->demo->platformAdmin, $this->demo->campus, primary: true);
        app(GrantSchoolMembership::class)->grant($this->demo->platformAdmin, $this->demo->sisterCampus);

        if (Installation::withoutGlobalScopes()->doesntExist()) {
            Installation::create([
                'lock_key' => 'application',
                'installed_by' => $this->demo->platformAdmin->id,
                'organization_id' => $this->demo->organization->id,
                'school_id' => $this->demo->campus->id,
                'locale' => config('app.locale'),
                'demo_data_loaded' => true,
                'email_configured' => false,
                'installed_at' => now(),
            ]);
        }

        school_context()->set($this->demo->campus, remember: false);
    }

    private function campus(string $name, string $initials, string $address, string $postalCode): School
    {
        $school = School::create([
            'organization_id' => $this->demo->organization->id,
            'name' => $name,
            'initials' => $initials,
            'address' => $address,
            'country' => 'United States',
            'state' => 'Oregon',
            'city' => 'Portland',
            'postal_code' => $postalCode,
            'email' => 'office@'.Str::slug($initials).'.'.self::DOMAIN,
            'phone' => '(503) 555-0100',
            'code' => Str::upper(Str::random(10)),
            'setup_details_completed_at' => now(),
        ]);

        $school->operatingProfile()->create([
            'preset' => SchoolOperatingProfile::DEFAULT_PRESET,
            'labels' => SchoolOperatingProfile::labelsFor(SchoolOperatingProfile::DEFAULT_PRESET),
            'setup_completed_at' => now(),
        ]);

        return $school;
    }

    /**
     * A school year from late August to mid June, with a fall and a spring
     * semester. It is the year that runs today.
     */
    private function createCalendar(): void
    {
        $today = now()->startOfDay();
        $startYear = $today->month >= 8 ? $today->year : $today->year - 1;
        $startsOn = Carbon::create($startYear, 8, 24);
        $endsOn = Carbon::create($startYear + 1, 6, 11);

        $academicYear = app(SaveAcademicCalendar::class)->save(
            $this->demo->campus,
            $startsOn,
            $endsOn,
            [
                ['name' => 'Fall semester', 'type' => AcademicPeriodType::Semester->value, 'starts_on' => $startsOn->toDateString(), 'ends_on' => Carbon::create($startYear, 12, 18)->toDateString()],
                ['name' => 'Spring semester', 'type' => AcademicPeriodType::Semester->value, 'starts_on' => Carbon::create($startYear + 1, 1, 5)->toDateString(), 'ends_on' => $endsOn->toDateString()],
            ],
            $this->demo->platformAdmin,
        );

        $this->demo->academicYear = app(PublishAcademicCalendar::class)->publish($academicYear, $this->demo->platformAdmin);
        app(AcademicYearService::class)->setSchoolDefaultAcademicYear($this->demo->academicYear);

        foreach ($this->demo->academicYear->topLevelPeriods()->get() as $period) {
            $this->demo->periods[$period->name] = $period;
        }

        $this->demo->currentPeriod = $this->periodOn($today);
        $this->demo->campus->forceFill(['academic_period_id' => $this->demo->currentPeriod->id])->save();

        academic_period_context()->setAcademicYear($this->demo->academicYear, remember: false);
        academic_period_context()->setAcademicPeriod($this->demo->currentPeriod, remember: false);
    }

    /**
     * The semester that runs on a day, or the next one in a break.
     */
    private function periodOn(Carbon $day): AcademicPeriod
    {
        foreach ($this->demo->periods as $period) {
            if ($day->lessThanOrEqualTo($period->ends_on)) {
                return $period;
            }
        }

        return end($this->demo->periods);
    }

    private function createStaff(): void
    {
        $this->demo->schoolAdmin = $this->person('David Nguyen', 'david.nguyen');
        $this->join($this->demo->schoolAdmin, Role::Admin->value);

        foreach (self::SUBJECTS as $subject => $teacherName) {
            $teacher = $this->person($teacherName, Str::slug($teacherName, '.'));
            $this->join($teacher, Role::Teacher->value);
            $teacher->teacherRecord()->create(['user_id' => $teacher->id]);
            $this->demo->teachers[$subject] = $teacher;
        }

        $this->demo->accountant = $this->person('James Patel', 'james.patel');
        $this->join($this->demo->accountant, 'accountant');

        $this->demo->librarian = $this->person('Emily Rodriguez', 'emily.rodriguez');
        $this->join($this->demo->librarian, 'librarian');
    }

    /**
     * Grades 9 to 12, each with an A and a B section and a homeroom teacher.
     */
    private function createStructure(): void
    {
        $homeroomTeachers = array_values($this->demo->teachers);

        foreach (self::GRADES as $position => $grade) {
            $level = app(CreateAcademicLevel::class)->create($grade, 'G'.($position + 9), position: $position, actor: $this->demo->platformAdmin);
            $this->demo->levels[$grade] = $level;

            foreach (['A', 'B'] as $index => $letter) {
                $name = ($position + 9).$letter;
                $section = app(CreateAcademicCycleSection::class)->create(
                    $this->demo->academicYear,
                    $level,
                    $name,
                    ['room' => 'Room '.(100 * ($position + 1) + $index + 1), 'capacity' => 28, 'position' => $index],
                    $homeroomTeachers[($position * 2 + $index) % count($homeroomTeachers)],
                    $this->demo->platformAdmin,
                );
                $this->demo->sections[$name] = app(ChangeAcademicCycleSectionStatus::class)->change($section, AcademicStructureStatus::Active, $this->demo->platformAdmin);
            }
        }

        foreach (array_keys(self::SUBJECTS) as $subject) {
            $this->demo->subjects[$subject] = Subject::create([
                'name' => $subject,
                'short_name' => Str::upper(Str::substr(Str::replace(['.', ' '], '', $subject), 0, 4)),
                'school_id' => $this->demo->campus->id,
            ]);
        }
    }

    /**
     * Five learners in each section, each with a guardian who shares their
     * family name.
     */
    private function createStudents(): void
    {
        $students = app(StudentService::class);
        $links = app(ChangeGuardianLink::class);
        $admittedOn = $this->demo->academicYear->starts_on->toDateString();
        $number = 0;

        foreach ($this->demo->sections as $sectionName => $section) {
            foreach (range(1, 5) as $seat) {
                $first = self::FIRST_NAMES[$number % count(self::FIRST_NAMES)];
                $last = self::LAST_NAMES[$number % count(self::LAST_NAMES)];
                $number++;

                $isDemoStudent = $sectionName === '10A' && $seat === 1;
                [$first, $last] = $isDemoStudent ? ['Ethan', 'Brooks'] : [$first, $last];

                $student = $this->person("$first $last", Str::lower("$first.$last"), 'student');
                app(GrantSchoolMembership::class)->grant($student, $this->demo->campus);
                $student->assignRole(Role::Student);
                $students->createStudentRecord($student, [
                    'academic_cycle_section_id' => $section->id,
                    'admission_date' => $admittedOn,
                    'admission_number' => 'RHS-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                ]);
                $this->demo->students[$sectionName][] = $student;

                $guardianFirst = $isDemoStudent ? 'Laura' : self::FIRST_NAMES[(count(self::FIRST_NAMES) - $number) % count(self::FIRST_NAMES)];
                $guardian = $this->person("$guardianFirst $last", Str::lower("$guardianFirst.$last"), 'family');
                $this->join($guardian, Role::Parent->value);
                $guardian->parentRecord()->firstOrCreate(['user_id' => $guardian->id]);
                $links->link($guardian, $student, $this->demo->platformAdmin);
                $this->demo->guardians[] = $guardian;

                if ($isDemoStudent) {
                    $this->demo->demoStudent = $student;
                    $this->demo->demoParent = $guardian;
                }
            }
        }
    }

    /**
     * Every subject is taught to every section this semester, by the teacher
     * of that subject.
     */
    private function createOfferings(): void
    {
        foreach ($this->demo->subjects as $subjectName => $subject) {
            foreach ($this->demo->levels as $grade => $level) {
                $sectionIds = collect($this->demo->sections)
                    ->filter(fn ($section): bool => $section->academic_level_id === $level->id)
                    ->pluck('id')
                    ->all();

                $offerings = app(CreateCourseOfferingsForSections::class)->create(
                    $subject,
                    $this->demo->academicYear,
                    $this->demo->currentPeriod->id,
                    $level,
                    $sectionIds,
                    plannedPeriodsPerWeek: 4,
                    actor: $this->demo->platformAdmin,
                );

                foreach ($offerings as $offering) {
                    $offering = app(ChangeCourseOfferingStatus::class)->change($offering, CourseOfferingStatus::Active, $this->demo->platformAdmin);
                    app(AssignTeacher::class)->assign($offering, $this->demo->teachers[$subjectName], actor: $this->demo->platformAdmin);
                    $this->demo->offerings[] = $offering;
                }
            }
        }
    }

    /**
     * A signed-up person who can sign in with the demo password.
     */
    private function person(string $name, string $mailbox, string $subdomain = 'staff'): User
    {
        $user = User::create([
            'name' => $name,
            'email' => "$mailbox@$subdomain.".self::DOMAIN,
            'password' => $this->password,
            'country' => 'United States',
            'state' => 'Oregon',
            'city' => 'Portland',
        ]);

        $user->forceFill([
            'account_status' => AccountStatus::Active,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }

    /**
     * Make a person a member of the main campus with one role there.
     */
    private function join(User $user, string $role): void
    {
        app(GrantSchoolMembership::class)->grant($user, $this->demo->campus, primary: true);
        $user->assignRole($role);
    }
}
