<?php

namespace Database\Seeders\Demo;

use App\Actions\Academic\PublishAcademicCalendar;
use App\Actions\Academic\SaveAcademicCalendar;
use App\Actions\Admissions\JoinWaitlist;
use App\Actions\Admissions\OfferNextWaitlistEntry;
use App\Actions\Curriculum\ChangeAcademicCycleSectionStatus;
use App\Actions\Curriculum\CreateAcademicCycleSection;
use App\Actions\Curriculum\CreateAcademicLevel;
use App\Actions\Curriculum\UpdateAcademicCycleSection;
use App\Actions\Enrollment\RequestCampusMove;
use App\Actions\Identity\SendAccountInvitation;
use App\Actions\Portal\SubmitPortalRequest;
use App\Actions\Report\RequestReport;
use App\Actions\School\GrantSchoolMembership;
use App\Actions\Sharing\RequestDataSharing;
use App\Enums\AcademicStructureStatus;
use App\Enums\AccountStatus;
use App\Enums\DataCategory;
use App\Enums\PortalRequestType;
use App\Enums\Role;
use App\Jobs\BuildReport;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Academic\SectionSeats;
use App\Services\AcademicYear\AcademicYearService;
use App\Services\Import\ImportRunner;
use App\Services\Student\StudentService;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;

/**
 * The office work of the district: invitations, admissions, moves between
 * campuses, record sharing, imports, report exports and family requests.
 *
 * The sister campus gets a small school year of its own here, because a move
 * and a sharing request need a learner who attends it.
 */
class Administration implements DemoPart
{
    /**
     * The mailbox domain of the rows in the checked import.
     *
     * The import checks every address with `email:rfc,dns`, and the demo
     * domain has no DNS record, so every row would fail. A public throwaway
     * inbox passes the check and never reaches a real family.
     */
    private const IMPORT_MAIL_DOMAIN = 'mailinator.com';

    private DemoSchool $demo;

    private string $password;

    /** The principal of the sister campus. */
    private User $sisterPrincipal;

    /** The Maple Grove learner who is moving up to the high school. */
    private StudentRecord $movingLearner;

    public function __construct(
        private SaveAcademicCalendar $saveAcademicCalendar,
        private PublishAcademicCalendar $publishAcademicCalendar,
        private AcademicYearService $academicYears,
        private CreateAcademicLevel $createAcademicLevel,
        private CreateAcademicCycleSection $createAcademicCycleSection,
        private ChangeAcademicCycleSectionStatus $changeSectionStatus,
        private UpdateAcademicCycleSection $updateAcademicCycleSection,
        private GrantSchoolMembership $grantSchoolMembership,
        private StudentService $students,
        private SendAccountInvitation $sendAccountInvitation,
        private JoinWaitlist $joinWaitlist,
        private OfferNextWaitlistEntry $offerNextWaitlistEntry,
        private SectionSeats $seats,
        private RequestCampusMove $requestCampusMove,
        private RequestDataSharing $requestDataSharing,
        private ImportRunner $imports,
        private RequestReport $requestReport,
        private SubmitPortalRequest $portalRequests,
    ) {}

    public function seed(DemoSchool $demo): void
    {
        $this->demo = $demo;
        $this->password = Hash::make((string) config('demo.password'));

        $this->openSisterCampus();
        $this->inviteNewTeacher();
        $this->fillAdmissionQueue();
        $this->requestCampusMove();
        $this->shareRecords();
        $this->checkImport();
        $this->exportReport();
        $this->sendFamilyRequests();
    }

    /**
     * Give Maple Grove the same school year as the high school, an eighth
     * grade, a principal and a learner who is moving up.
     */
    private function openSisterCampus(): void
    {
        $sister = $this->demo->sisterCampus;
        $actor = $this->demo->platformAdmin;

        school_context()->set($sister, remember: false);

        try {
            $academicYear = $this->publishAcademicCalendar->publish(
                $this->saveAcademicCalendar->save(
                    $sister,
                    $this->demo->academicYear->starts_on->copy(),
                    $this->demo->academicYear->ends_on->copy(),
                    array_values(array_map(fn (AcademicPeriod $period): array => [
                        'name' => $period->name,
                        'type' => $period->type->value,
                        'starts_on' => $period->starts_on->toDateString(),
                        'ends_on' => $period->ends_on->toDateString(),
                    ], $this->demo->periods)),
                    $actor,
                ),
                $actor,
            );
            $this->academicYears->setSchoolDefaultAcademicYear($academicYear);

            /** @var AcademicPeriod $currentPeriod */
            $currentPeriod = $academicYear->topLevelPeriods()->where('name', $this->demo->currentPeriod->name)->firstOrFail();
            $sister->forceFill(['academic_period_id' => $currentPeriod->id])->save();

            academic_period_context()->setAcademicYear($academicYear, remember: false);
            academic_period_context()->setAcademicPeriod($currentPeriod, remember: false);

            $this->sisterPrincipal = $this->person('Karen Whitfield', 'karen.whitfield');
            $this->grantSchoolMembership->grant($this->sisterPrincipal, $sister, primary: true);
            $this->sisterPrincipal->assignRole(Role::Admin->value);

            $section = $this->sisterSection($academicYear, $actor);

            $learner = $this->person('Sofia Ramirez', 'sofia.ramirez', 'student');
            $this->grantSchoolMembership->grant($learner, $sister, primary: true);
            $learner->assignRole(Role::Student);
            $this->students->createStudentRecord($learner, [
                'academic_cycle_section_id' => $section->id,
                'admission_date' => $academicYear->starts_on->toDateString(),
                'admission_number' => 'MGM-0001',
            ]);

            $this->movingLearner = StudentRecord::query()
                ->where('school_id', $sister->id)
                ->where('user_id', $learner->id)
                ->sole();
        } finally {
            $this->returnToMainCampus();
        }
    }

    private function sisterSection(AcademicYear $academicYear, User $actor): AcademicCycleSection
    {
        $level = $this->createAcademicLevel->create('Grade 8', 'G8', position: 0, actor: $actor);
        $section = $this->createAcademicCycleSection->create(
            $academicYear,
            $level,
            '8A',
            ['room' => 'Room 204', 'capacity' => 30, 'position' => 0],
            actor: $actor,
        );

        return $this->changeSectionStatus->change($section, AcademicStructureStatus::Active, $actor);
    }

    /**
     * A chemistry teacher the principal invited yesterday, who has not set a
     * password yet.
     */
    private function inviteNewTeacher(): void
    {
        $teacher = $this->person('Jasmine Wright', 'jasmine.wright', withPassword: false);
        $this->grantSchoolMembership->grant($teacher, $this->demo->campus, primary: true);
        $teacher->assignRole(Role::Teacher->value);
        $teacher->teacherRecord()->create(['user_id' => $teacher->id]);

        $this->at(now()->subDay()->setTime(15, 40), fn () => $this->sendAccountInvitation->send($teacher, $this->demo->schoolAdmin));
    }

    /**
     * Grade 9B is full. One family was offered the seat a learner left, and
     * another family waits behind them.
     */
    private function fillAdmissionQueue(): void
    {
        $section = $this->demo->sections['9B'];
        $principal = $this->demo->schoolAdmin;

        $this->setCapacity($section, $this->seats->taken($section));

        $this->at(now()->subDays(12)->setTime(10, 15), fn () => $this->joinWaitlist->join($section, $this->applicant('Jacob Morales', 'jacob.morales'), $principal, priority: 1));
        $this->at(now()->subDays(9)->setTime(13, 5), fn () => $this->joinWaitlist->join($section, $this->applicant('Maya Chen', 'maya.chen'), $principal));

        $this->setCapacity($section, $this->seats->taken($section) + 1);
        $this->at(now()->subDays(2)->setTime(9, 30), fn () => $this->offerNextWaitlistEntry->offer($section, $principal));
    }

    private function setCapacity(AcademicCycleSection $section, int $capacity): void
    {
        $section->refresh();

        $this->updateAcademicCycleSection->update($section, [
            'name' => $section->name,
            'label' => $section->label,
            'stream' => $section->stream,
            'shift' => $section->shift,
            'language' => $section->language,
            'room' => $section->room,
            'capacity' => $capacity,
            'position' => $section->position,
        ], $section->homeroomTeacher, $this->demo->platformAdmin);
    }

    /**
     * A family that applied to the high school and has no enrollment yet.
     */
    private function applicant(string $name, string $mailbox): User
    {
        $applicant = $this->person($name, $mailbox, 'family', withPassword: false);
        $this->grantSchoolMembership->grant($applicant, $this->demo->campus, primary: true);

        return $applicant;
    }

    /**
     * Maple Grove asks the high school to take Sofia, who skips a grade.
     * Riverside High School still has to decide.
     */
    private function requestCampusMove(): void
    {
        $this->at(now()->subDays(3)->setTime(11, 0), fn () => $this->requestCampusMove->request(
            $this->movingLearner,
            $this->demo->sections['9A'],
            $this->sisterPrincipal,
            'Sofia finished the eighth-grade program early. The district approved her move to ninth grade.',
            now()->addWeek()->startOfWeek(),
        ));
    }

    /**
     * The high school asks Maple Grove for Sofia's records before she
     * arrives, and Maple Grove asked for the allergy plan of a high school
     * tutor, which the high school approved.
     */
    private function shareRecords(): void
    {
        $principal = $this->demo->schoolAdmin;

        $this->at(now()->subDays(2)->setTime(14, 10), fn () => $this->requestDataSharing->request(
            $this->movingLearner,
            $this->demo->campus,
            "Plan Sofia's ninth-grade schedule and support before her campus move.",
            [DataCategory::AcademicResults, DataCategory::Attendance, DataCategory::Health],
            now()->addMonth()->toDateString(),
            $principal,
        ));

        $tutor = $this->demo->students['11A'][0];
        $tutorRecord = StudentRecord::query()
            ->where('school_id', $this->demo->campus->id)
            ->where('user_id', $tutor->id)
            ->sole();
        $firstName = explode(' ', $tutor->name)[0];

        $request = $this->at(now()->subDays(8)->setTime(16, 20), fn () => $this->requestDataSharing->request(
            $tutorRecord,
            $this->demo->sisterCampus,
            "$firstName tutors in our after-school reading program. Our nurse needs the allergy plan on file.",
            [DataCategory::Health],
            now()->addMonths(4)->toDateString(),
            $this->sisterPrincipal,
        ));

        $this->at(now()->subDays(7)->setTime(8, 45), fn () => $this->requestDataSharing->approve(
            $request,
            $principal,
            'Approved for the after-school program. The allergy plan only.',
        ));
    }

    /**
     * A student file from the spring transfer list, checked but not written.
     * Two of its rows need fixing.
     */
    private function checkImport(): void
    {
        $level = 'Grade 9';
        $mail = fn (string $mailbox): string => "$mailbox@".self::IMPORT_MAIL_DOMAIN;
        $birthday = fn (int $yearsAgo, int $month, int $day): string => Carbon::create(now()->year - $yearsAgo, $month, $day)->toDateString();

        $rows = [
            ['name' => 'Isabel Romero', 'email' => $mail('isabel.romero.rhs'), 'birthday' => $birthday(15, 3, 14), 'gender' => 'Female', 'level' => $level, 'section' => '9A'],
            ['name' => 'Tyler Brennan', 'email' => $mail('tyler.brennan.rhs'), 'birthday' => $birthday(15, 7, 2), 'gender' => 'Male', 'level' => $level, 'section' => '9B'],
            ['name' => 'Hailey Sutton', 'email' => '', 'birthday' => $birthday(14, 11, 23), 'gender' => 'Female', 'level' => $level, 'section' => '9A'],
            ['name' => 'Marcus Webb', 'email' => $mail('marcus.webb.rhs'), 'birthday' => $birthday(-5, 5, 9), 'gender' => 'Male', 'level' => $level, 'section' => '9B'],
            ['name' => 'Priya Desai', 'email' => $mail('priya.desai.rhs'), 'birthday' => $birthday(15, 1, 30), 'gender' => 'Female', 'level' => $level, 'section' => '9A'],
        ];

        $this->at(now()->subHours(3), fn () => $this->imports->stage('students', $rows, 'spring-transfers.csv', $this->demo->schoolAdmin));
    }

    /**
     * A class list the principal exported this morning, ready to download.
     */
    private function exportReport(): void
    {
        $run = $this->at(now()->subHours(2), fn () => $this->requestReport->request('class-list', actor: $this->demo->schoolAdmin));

        // The demo queue may run on a worker. Build the file now, so the
        // export is ready when the page opens; the worker then skips it.
        $this->at(now()->subHours(2)->addMinute(), fn () => Bus::dispatchSync(new BuildReport($run->id)));
    }

    /**
     * Laura Brooks asked the school to fix Ethan's emergency contact today,
     * and the office already answered her request for an enrollment letter.
     */
    private function sendFamilyRequests(): void
    {
        $parent = $this->demo->demoParent;
        $enrollment = StudentRecord::query()
            ->where('school_id', $this->demo->campus->id)
            ->where('user_id', $this->demo->demoStudent->id)
            ->sole();

        $letter = $this->at(now()->subDays(6)->setTime(19, 5), fn () => $this->portalRequests->submit(
            $enrollment,
            'Enrollment letter for a summer program',
            PortalRequestType::Document,
            'Ethan is applying to a summer science camp. Could the office send a letter that confirms he attends Riverside High School?',
            $parent,
        ));

        $this->at(now()->subDays(5)->setTime(10, 30), fn () => $this->portalRequests->answer(
            $letter,
            'The letter is signed and ready. Pick it up at the front office, or we can email a PDF copy if you prefer.',
            $this->demo->schoolAdmin,
        ));

        $this->at(now()->subHours(4), fn () => $this->portalRequests->submit(
            $enrollment,
            "Update Ethan's emergency contact",
            PortalRequestType::Correction,
            'Our home phone changed. Please use (503) 555-0147 as the emergency contact number from now on.',
            $parent,
        ));
    }

    /**
     * Run the work as if it happened at the given moment, so the screens
     * show a believable history.
     *
     * @template TResult
     *
     * @param  callable(): TResult  $work
     * @return TResult
     */
    private function at(Carbon $moment, callable $work): mixed
    {
        $now = Carbon::getTestNow();
        Carbon::setTestNow($moment);

        try {
            return $work();
        } finally {
            Carbon::setTestNow($now);
        }
    }

    private function returnToMainCampus(): void
    {
        school_context()->set($this->demo->campus, remember: false);
        academic_period_context()->setAcademicYear($this->demo->academicYear, remember: false);
        academic_period_context()->setAcademicPeriod($this->demo->currentPeriod, remember: false);
    }

    /**
     * A person at the district's domain. A person without a password signs
     * in only after accepting an invitation.
     */
    private function person(string $name, string $mailbox, string $subdomain = 'staff', bool $withPassword = true): User
    {
        $user = User::create([
            'name' => $name,
            'email' => "$mailbox@$subdomain.".DemoSchoolSeeder::DOMAIN,
            'password' => $withPassword ? $this->password : null,
            'country' => 'United States',
            'state' => 'Oregon',
            'city' => 'Portland',
        ]);

        if ($withPassword) {
            $user->forceFill([
                'account_status' => AccountStatus::Active,
                'email_verified_at' => now(),
            ])->save();
        }

        return $user;
    }
}
