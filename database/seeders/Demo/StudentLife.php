<?php

namespace Database\Seeders\Demo;

use App\Actions\Boarding\AssignBoardingPlace;
use App\Actions\Boarding\AssignBoardingSupervisor;
use App\Actions\Boarding\AttachDormitoryToBoardingResidence;
use App\Actions\Boarding\CreateBoardingResidence;
use App\Actions\Boarding\LinkSchoolToBoardingResidence;
use App\Actions\Boarding\ManageBoardingHouse;
use App\Actions\Boarding\RecordBoardingRoll;
use App\Actions\Boarding\StartBoardingRoll;
use App\Actions\Cohort\ChangeProgramParticipation;
use App\Actions\Cohort\SaveProgram;
use App\Actions\Discipline\ReportIncident;
use App\Actions\Facility\BookFacility;
use App\Actions\Facility\ManageFacility;
use App\Actions\Library\IssueLoan;
use App\Actions\Library\ReturnLoan;
use App\Actions\Library\ShelveLibraryCopies;
use App\Actions\Notice\PublishNotice;
use App\Actions\Staff\ManageStaffLeave;
use App\Actions\Staff\ManageStaffProfile;
use App\Actions\Wellbeing\ManageSupportPlan;
use App\Enums\BoardingRollEntryStatus;
use App\Enums\BoardingRollType;
use App\Enums\CalendarEventType;
use App\Enums\EmploymentType;
use App\Enums\FacilityKind;
use App\Enums\Feature;
use App\Enums\IncidentCategory;
use App\Enums\IncidentParticipantRole;
use App\Enums\IncidentStatus;
use App\Enums\LeaveType;
use App\Enums\NoticeAudienceScope;
use App\Enums\ParticipationStatus;
use App\Enums\ProgramType;
use App\Enums\SupervisionRole;
use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Models\AcademicPeriod;
use App\Models\CalendarEvent;
use App\Models\DormitoryBed;
use App\Models\Facility;
use App\Models\LibraryCopy;
use App\Models\Notice;
use App\Models\ProgramParticipation;
use App\Models\StaffProfile;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Notice\NoticeService;
use Illuminate\Support\Carbon;

/**
 * Fill the parts of school life outside the classroom: the calendar, the
 * notice board, care and conduct, staff leave, clubs, rooms, boarding and
 * the library.
 *
 * Every date follows today, so the calendar and the dashboard always show
 * something in the coming days.
 */
class StudentLife implements DemoPart
{
    private DemoSchool $demo;

    public function seed(DemoSchool $demo): void
    {
        $this->demo = $demo;

        features()->enable(Feature::Boarding, $demo->campus, $demo->platformAdmin);
        features()->enable(Feature::Library, $demo->campus, $demo->platformAdmin);

        $this->createCalendarEvents();
        $this->createNotices();
        $this->createIncidents();
        $this->createSupportPlans();
        $this->createStaffLeave();
        $this->createProgrammes();
        $this->createFacilities();
        $this->createBoarding();
        $this->createLibrary();
    }

    /**
     * Spirit week runs today, the open house and the homecoming game fall in
     * the next seven days, and two days without classes follow.
     */
    private function createCalendarEvents(): void
    {
        $today = now()->startOfDay();
        $thanksgiving = Carbon::parse('fourth thursday of november '.$this->demo->academicYear->starts_on->year);

        $this->event('Spirit week', CalendarEventType::Activity, $today, $today->copy()->addDays(4), 'Theme days all week. Wear school colors on Friday.');
        $this->event('Fall open house', CalendarEventType::ParentMeeting, $this->schoolDay($today->copy()->addDays(3))->setTime(18, 0), $this->schoolDay($today->copy()->addDays(3))->setTime(20, 0), 'Families meet teachers and walk through a day of classes.', 'Main building', allDay: false);
        $this->event('Homecoming game', CalendarEventType::Activity, $today->copy()->addDays(6)->setTime(19, 0), $today->copy()->addDays(6)->setTime(21, 30), 'Riverside Hawks against the Lincoln Cardinals.', 'Hawks Stadium', allDay: false);
        $this->event('Staff development day', CalendarEventType::Closure, $this->schoolDay($today->copy()->addDays(12)), $this->schoolDay($today->copy()->addDays(12)), 'No classes for students. Teachers attend training.');
        $this->event('Thanksgiving break', CalendarEventType::Holiday, $thanksgiving, $thanksgiving->copy()->addDay(), 'School is closed on Thursday and Friday.');
        $this->event('Fall semester final exams', CalendarEventType::Examination, $this->demo->periods['Fall semester']->ends_on->copy()->subDays(4), $this->demo->periods['Fall semester']->ends_on->copy(), 'The exam schedule is posted in each classroom.', isPublished: false);
    }

    private function event(
        string $title,
        CalendarEventType $type,
        Carbon $startsAt,
        Carbon $endsAt,
        string $description,
        ?string $location = null,
        bool $allDay = true,
        bool $isPublished = true,
    ): void {
        CalendarEvent::create([
            'school_id' => $this->demo->campus->id,
            'academic_year_id' => $this->demo->academicYear->id,
            'academic_period_id' => $this->periodOn($startsAt)->id,
            'created_by' => $this->demo->schoolAdmin->id,
            'title' => $title,
            'type' => $type,
            'description' => $description,
            'location' => $location,
            'is_all_day' => $allDay,
            'is_published' => $isPublished,
            'starts_at' => $allDay ? $startsAt->copy()->startOfDay() : $startsAt,
            'ends_at' => $allDay ? $endsAt->copy()->endOfDay() : $endsAt,
        ]);
    }

    /**
     * Two notices on the board now, one waiting for its day, and a draft.
     */
    private function createNotices(): void
    {
        $publisher = app(PublishNotice::class);
        $today = now()->startOfDay();
        $pictureDay = $today->copy()->next(Carbon::THURSDAY);

        $publisher->publish($this->notice(
            'Picture day is Thursday',
            '<p>School photos are taken on '.$pictureDay->format('l, F j').' in the library. Order forms went home in backpacks this week.</p>',
            $today,
            $pictureDay,
        ), $this->demo->schoolAdmin);

        $publisher->publish($this->notice(
            'Fall sports physicals are due',
            '<p>Athletes need a current physical on file before their next practice. Bring the signed form to the front office.</p>',
            $today->copy()->subDays(3),
            $today->copy()->addDays(14),
        ), $this->demo->schoolAdmin);

        $concertDay = $today->copy()->addDays(10);
        $publisher->schedule($this->notice(
            'Winter concert tickets',
            '<p>Tickets for the winter concert go on sale in the front office. Each family may reserve up to six seats.</p>',
            $concertDay,
            $concertDay->copy()->addDays(30),
        ), $concertDay->copy()->setTime(8, 0), $this->demo->schoolAdmin);

        $this->notice(
            'Parent-teacher conference sign-up',
            '<p>Conference slots open next month. Choose a time with each of your child’s teachers.</p>',
            $today->copy()->addDays(20),
            $today->copy()->addDays(35),
        );
    }

    private function notice(string $title, string $content, Carbon $startsOn, Carbon $stopsOn): Notice
    {
        return app(NoticeService::class)->storeNotice([
            'title' => $title,
            'content' => $content,
            'start_date' => $startsOn->toDateString(),
            'stop_date' => $stopsOn->toDateString(),
            'audience' => [
                'scope' => NoticeAudienceScope::School->value,
                'academic_level_ids' => [],
                'academic_cycle_section_ids' => [],
                'include_guardians' => true,
            ],
        ]);
    }

    /**
     * A small case that is finished, and one the dean is still looking at.
     */
    private function createIncidents(): void
    {
        $incidents = app(ReportIncident::class);
        $admin = $this->demo->schoolAdmin;

        $phone = $incidents->report(
            summary: 'Phone use during a quiz',
            category: IncidentCategory::Behaviour,
            description: 'A student checked messages during the unit quiz after being asked to put the phone away.',
            occurredAt: $this->schoolDay(now()->subDays(9))->setTime(10, 15),
            participants: [['enrollment' => $this->enrollment($this->demo->students['11A'][1]), 'role' => IncidentParticipantRole::Subject]],
            reporter: $this->demo->teachers['Algebra'],
            assignee: $admin,
            location: 'Room 301',
        );
        $incidents->changeStatus($phone, IncidentStatus::UnderReview, $admin);
        $incidents->addAction($phone, 'Conversation', 'Talk with the student about the phone policy during tests.', actor: $admin);
        $incidents->changeStatus($phone, IncidentStatus::Resolved, $admin, 'The student apologized and retook the quiz after school.');

        $lunch = $incidents->report(
            summary: 'Disagreement in the cafeteria',
            category: IncidentCategory::Behaviour,
            description: 'Two students argued loudly over a lunch table. A teacher on duty separated them before it went further.',
            occurredAt: $this->schoolDay(now()->subDays(2))->setTime(12, 20),
            participants: [
                ['enrollment' => $this->enrollment($this->demo->students['9B'][2]), 'role' => IncidentParticipantRole::Subject],
                ['enrollment' => $this->enrollment($this->demo->students['9B'][3]), 'role' => IncidentParticipantRole::Subject],
                ['user' => $this->demo->teachers['Physical Education'], 'role' => IncidentParticipantRole::Witness],
            ],
            reporter: $this->demo->teachers['Physical Education'],
            assignee: $admin,
            location: 'Cafeteria',
        );
        $incidents->changeStatus($lunch, IncidentStatus::UnderReview, $admin);
        $incidents->addAction($lunch, 'Restorative meeting', 'Meet with both students and the counselor to agree on next steps.', now()->addDays(3), $admin, $admin);
    }

    /**
     * Two plans in progress, each with steps done and one still to do.
     */
    private function createSupportPlans(): void
    {
        $plans = app(ManageSupportPlan::class);
        $admin = $this->demo->schoolAdmin;
        $today = now()->startOfDay();

        $extendedTime = $plans->open(
            $this->enrollment($this->demo->students['10B'][0]),
            'Extended time on tests',
            SupportCategory::Accommodation,
            'Time and a half on quizzes and unit tests, with a quiet room when one is free.',
            $today->copy()->subDays(30),
            $today->copy()->addDays(21),
            $this->demo->teachers['English Language Arts'],
            $admin,
        );
        $plans->changeStatus($extendedTime, SupportPlanStatus::Active, $admin);
        $plans->completeAction($plans->addAction($extendedTime, 'Share the accommodation with every teacher of the student.', $today->copy()->subDays(25), actor: $admin), $admin);
        $plans->completeAction($plans->addAction($extendedTime, 'Reserve the library study room for unit tests.', $today->copy()->subDays(20), actor: $admin), $admin);
        $plans->addAction($extendedTime, 'Review test results with the family.', $today->copy()->addDays(21), actor: $admin);
        $plans->addNote($extendedTime, 'Scores on the last two unit tests went up. The student says the quiet room helps.', $admin);

        $reading = $plans->open(
            $this->enrollment($this->demo->students['9A'][3]),
            'Reading fluency support',
            SupportCategory::Intervention,
            'Three short reading sessions a week with a peer tutor.',
            $today->copy()->subDays(14),
            $today->copy()->addDays(30),
            $this->demo->teachers['English Language Arts'],
            $admin,
        );
        $plans->changeStatus($reading, SupportPlanStatus::Active, $admin);
        $plans->completeAction($plans->addAction($reading, 'Match the student with a peer tutor.', $today->copy()->subDays(10), actor: $admin), $admin);
        $plans->addAction($reading, 'Repeat the fluency check.', $today->copy()->addDays(28), actor: $admin);
    }

    /**
     * Three teachers ask for days away. The principal approves one, declines
     * one and has not answered the third.
     */
    private function createStaffLeave(): void
    {
        $leave = app(ManageStaffLeave::class);
        $admin = $this->demo->schoolAdmin;
        $today = now()->startOfDay();

        $approved = $leave->request($this->staffProfile('Biology', 'RHS-T03'), $this->schoolDay($today->copy()->addDays(15)), $this->schoolDay($today->copy()->addDays(15))->addDay(), LeaveType::Annual, 'Family wedding in Seattle.', $this->demo->teachers['Biology']);
        $leave->approve($approved, $admin, 'Approved. Cover is arranged.');

        $declined = $leave->request($this->staffProfile('Physical Education', 'RHS-T07'), $this->schoolDay($today->copy()->addDays(6)), $this->schoolDay($today->copy()->addDays(6)), LeaveType::Unpaid, 'Personal day.', $this->demo->teachers['Physical Education']);
        $leave->decline($declined, $admin, 'This is homecoming week. Please choose another day.');

        $leave->request($this->staffProfile('Algebra', 'RHS-T02'), $this->schoolDay($today->copy()->addDays(22)), $this->schoolDay($today->copy()->addDays(22))->addDay(), LeaveType::Study, 'State mathematics teachers conference in Salem.', $this->demo->teachers['Algebra']);
    }

    /**
     * The employment record of a teacher, made the first time it is needed.
     */
    private function staffProfile(string $subject, string $staffNumber): StaffProfile
    {
        $teacher = $this->demo->teachers[$subject];
        $existing = StaffProfile::query()->inSchool()->where('user_id', $teacher->id)->first();

        return $existing ?? app(ManageStaffProfile::class)->create([
            'user_id' => $teacher->id,
            'staff_number' => $staffNumber,
            'job_title' => "$subject teacher",
            'department' => $subject,
            'employment_type' => EmploymentType::FullTime->value,
            'joined_on' => $this->demo->academicYear->starts_on->toDateString(),
        ], $this->demo->schoolAdmin);
    }

    /**
     * A club and an honours programme, with learners in every state of a
     * place.
     */
    private function createProgrammes(): void
    {
        $programmes = app(SaveProgram::class);
        $places = app(ChangeProgramParticipation::class);
        $admin = $this->demo->schoolAdmin;
        $startsOn = $this->demo->academicYear->starts_on->copy()->addWeeks(2);

        $robotics = $programmes->create(['name' => 'Robotics club', 'type' => ProgramType::Club->value, 'description' => 'Students design, build and program a robot for the regional competition in March.'], $admin);
        $roboticsMembers = [
            [$this->demo->students['10A'][0], ParticipationStatus::Active],
            [$this->demo->students['10B'][1], ParticipationStatus::Active],
            [$this->demo->students['11B'][2], ParticipationStatus::Active],
            [$this->demo->students['9A'][1], ParticipationStatus::Requested],
            [$this->demo->students['12A'][4], ParticipationStatus::Withdrawn],
        ];

        foreach ($roboticsMembers as [$student, $status]) {
            $place = $places->join($robotics, $this->enrollment($student), $startsOn, $this->demo->teachers['Algebra'], 'Tuesdays and Thursdays, 3:30 to 5:00 PM', $admin);
            $this->moveTo($places, $place, $status);
        }

        $scholars = $programmes->create(['name' => 'AP Scholars', 'type' => ProgramType::Special->value, 'description' => 'Juniors and seniors taking three or more Advanced Placement courses meet for study skills and exam preparation.'], $admin);
        $scholarMembers = [
            [$this->demo->students['11A'][0], ParticipationStatus::Active],
            [$this->demo->students['11A'][3], ParticipationStatus::Active],
            [$this->demo->students['12A'][0], ParticipationStatus::Active],
            [$this->demo->students['12B'][1], ParticipationStatus::Completed],
            [$this->demo->students['12B'][3], ParticipationStatus::Requested],
        ];

        foreach ($scholarMembers as [$student, $status]) {
            $place = $places->join($scholars, $this->enrollment($student), $startsOn, $this->demo->teachers['U.S. History'], 'Wednesdays at lunch', $admin);
            $this->moveTo($places, $place, $status);
        }
    }

    private function moveTo(ChangeProgramParticipation $places, ProgramParticipation $place, ParticipationStatus $status): void
    {
        if ($status === ParticipationStatus::Requested) {
            return;
        }

        $place = $places->changeStatus($place, ParticipationStatus::Active, actor: $this->demo->schoolAdmin);

        if ($status !== ParticipationStatus::Active) {
            $note = $status === ParticipationStatus::Completed ? 'Finished the programme early after passing the practice exams.' : 'Left to join the fall musical.';
            $places->changeStatus($place, $status, $note, actor: $this->demo->schoolAdmin);
        }
    }

    /**
     * Rooms and equipment the school books, with bookings in the coming days
     * and one bus out of use.
     */
    private function createFacilities(): void
    {
        $booking = app(BookFacility::class);
        $today = now()->startOfDay();

        $gym = $this->facility('Gymnasium', FacilityKind::Hall, 400, 'Bleachers seat 400. Book through the athletics office.');
        $studyRoom = $this->facility('Library study room', FacilityKind::Classroom, 8, 'Quiet room with a whiteboard and a wall screen.');
        $lab = $this->facility('Science lab', FacilityKind::Laboratory, 28, 'Fume hood and eye wash station. A teacher must be present.');
        $this->facility('Chromebook cart', FacilityKind::Equipment, 30, 'Thirty charged Chromebooks.');
        $bus = $this->facility('Activity bus', FacilityKind::Vehicle, 48, 'Needs a driver with a commercial license.');

        $booking->book($gym, $today->copy()->addDays(5)->setTime(14, 0), $today->copy()->addDays(5)->setTime(15, 0), 'Homecoming pep rally', $this->demo->teachers['Physical Education']);
        $booking->book($studyRoom, $today->copy()->addDay()->setTime(15, 30), $today->copy()->addDay()->setTime(16, 30), 'Peer tutoring', $this->demo->teachers['English Language Arts']);
        $booking->book($lab, $today->copy()->addDays(2)->setTime(15, 30), $today->copy()->addDays(2)->setTime(17, 0), 'Robotics club build session', $this->demo->teachers['Algebra']);
        $booking->book($lab, $today->copy()->addDays(8)->setTime(9, 0), $today->copy()->addDays(8)->setTime(11, 0), 'Biology dissection lab', $this->demo->teachers['Biology']);

        app(ManageFacility::class)->retire($bus, $this->demo->schoolAdmin);
    }

    private function facility(string $name, FacilityKind $kind, int $capacity, string $notes): Facility
    {
        return Facility::create([
            'school_id' => $this->demo->campus->id,
            'name' => $name,
            'kind' => $kind,
            'capacity' => $capacity,
            'notes' => $notes,
        ]);
    }

    /**
     * A district residence hall with one house of boarders, last night's
     * completed roll and this morning's roll half taken.
     */
    private function createBoarding(): void
    {
        $admin = $this->demo->platformAdmin;
        $residence = app(CreateBoardingResidence::class)->create($this->demo->organization, 'Riverside Residence Hall', 'Shared by the district for students who live far from campus.', $admin);
        app(LinkSchoolToBoardingResidence::class)->link($residence, $this->demo->campus, $admin);

        $house = app(ManageBoardingHouse::class)->open($this->demo->campus->id, 'Cedar House', 'House', 'Second floor of Riverside Residence Hall.', 3, 2);
        app(AttachDormitoryToBoardingResidence::class)->attach($residence, $house, $admin);
        app(AssignBoardingSupervisor::class)->assign($house, $this->demo->teachers['Physical Education'], SupervisionRole::Warden, $this->demo->academicYear->starts_on->copy(), $admin);

        $beds = DormitoryBed::query()
            ->whereHas('room', fn ($room) => $room->where('dormitory_id', $house->id))
            ->orderBy('id')
            ->get();
        $boarders = [
            $this->demo->students['11A'][2],
            $this->demo->students['11B'][0],
            $this->demo->students['12A'][1],
            $this->demo->students['12A'][3],
            $this->demo->students['12B'][2],
        ];

        foreach ($boarders as $index => $student) {
            app(AssignBoardingPlace::class)->assign($this->enrollment($student), $beds[$index], $admin, 'Lives outside the district bus routes.', $this->demo->academicYear->starts_on->copy());
        }

        $rolls = app(StartBoardingRoll::class);
        $recorder = app(RecordBoardingRoll::class);
        $warden = $this->demo->teachers['Physical Education'];

        $evening = $rolls->start($house, BoardingRollType::Evening, now()->subDay()->toDateString(), $warden);
        $answers = [BoardingRollEntryStatus::Present, BoardingRollEntryStatus::Present, BoardingRollEntryStatus::Late, BoardingRollEntryStatus::Present, BoardingRollEntryStatus::Away];
        $recorder->record($evening, $evening->entries()->orderBy('id')->get()->values()->map(fn ($entry, int $index): array => [
            'id' => $entry->id,
            'status' => $answers[$index]->value,
            'location' => $answers[$index] === BoardingRollEntryStatus::Away ? 'Away game with the soccer team' : null,
            'note' => $answers[$index] === BoardingRollEntryStatus::Late ? 'Back at 9:20 PM from the library.' : null,
        ])->all(), complete: true, actor: $warden);

        $morning = $rolls->start($house, BoardingRollType::Morning, now()->toDateString(), $warden);
        $recorder->record($morning, $morning->entries()->orderBy('id')->limit(3)->get()->map(fn ($entry): array => [
            'id' => $entry->id,
            'status' => BoardingRollEntryStatus::Present->value,
        ])->all(), actor: $warden);
    }

    /**
     * Classic novels on the shelf, with loans out and two overdue.
     */
    private function createLibrary(): void
    {
        $shelve = app(ShelveLibraryCopies::class);
        $books = [
            ['Pride and Prejudice', 'Jane Austen', 'LIB-1001', 'FIC AUS'],
            ['The Adventures of Huckleberry Finn', 'Mark Twain', 'LIB-1002', 'FIC TWA'],
            ['Frankenstein', 'Mary Shelley', 'LIB-1003', 'FIC SHE'],
            ['The Call of the Wild', 'Jack London', 'LIB-1004', 'FIC LON'],
            ['Little Women', 'Louisa May Alcott', 'LIB-1005', 'FIC ALC'],
            ['The Scarlet Letter', 'Nathaniel Hawthorne', 'LIB-1006', 'FIC HAW'],
        ];

        /** @var array<string, LibraryCopy> $copies */
        $copies = [];

        foreach ($books as [$title, $author, $barcode, $shelfMark]) {
            $copies[$barcode] = $shelve->shelve(
                $this->demo->campus,
                null,
                ['title' => $title, 'authors' => $author, 'isbn' => null, 'category' => 'Fiction'],
                $barcode,
                2,
                $shelfMark,
            )->firstOrFail();
        }

        $loans = app(IssueLoan::class);
        $librarian = $this->demo->librarian;

        $loans->issue($copies['LIB-1001'], $this->demo->demoStudent, $librarian, now()->subDays(5));
        $loans->issue($copies['LIB-1002'], $this->demo->students['11A'][1], $librarian, now()->subDays(10));
        $loans->issue($copies['LIB-1003'], $this->demo->students['12B'][0], $librarian, now()->subDays(20));
        $loans->issue($copies['LIB-1004'], $this->demo->students['9B'][4], $librarian, now()->subDays(18));
        $loans->issue($copies['LIB-1005'], $this->demo->teachers['English Language Arts'], $librarian, now()->subDays(2));

        $returned = $loans->issue($copies['LIB-1006'], $this->demo->students['10B'][2], $librarian, now()->subDays(16));
        app(ReturnLoan::class)->receive($returned, $librarian, now()->subDays(3));
    }

    /**
     * The learner's enrollment at the main campus.
     */
    private function enrollment(User $student): StudentRecord
    {
        return StudentRecord::query()->inSchool()->where('user_id', $student->id)->firstOrFail();
    }

    /**
     * The semester a day falls in, or the last one after the year ends.
     */
    private function periodOn(Carbon $day): AcademicPeriod
    {
        foreach ($this->demo->periods as $period) {
            if ($day->lessThanOrEqualTo($period->ends_on->copy()->endOfDay())) {
                return $period;
            }
        }

        return $this->demo->currentPeriod;
    }

    /**
     * The day itself, or the Monday after when it falls on a weekend.
     */
    private function schoolDay(Carbon $day): Carbon
    {
        return $day->isWeekend() ? $day->copy()->next(Carbon::MONDAY) : $day->copy();
    }
}
