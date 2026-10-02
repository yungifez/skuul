<?php

namespace Database\Seeders\Demo;

use App\Actions\Attendance\RecordAttendance;
use App\Actions\Exam\SaveExam;
use App\Actions\Gradebook\ApproveResult;
use App\Actions\Gradebook\PublishResult;
use App\Actions\Gradebook\RecordGrade;
use App\Actions\Gradebook\SaveGradingScale;
use App\Actions\Graduation\ManageGraduationPlan;
use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Actions\Syllabus\SubmitSyllabus;
use App\Actions\Timetable\PublishTimetable;
use App\Actions\Timetable\ReviseTimetable;
use App\Enums\AttendanceStatus;
use App\Enums\GradeAggregation;
use App\Enums\GradeEntryState;
use App\Enums\GradeItemType;
use App\Enums\GradingScaleType;
use App\Enums\TopicCoverageStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicPeriod;
use App\Models\CourseOffering;
use App\Models\CustomTimetableItem;
use App\Models\GradeCategory;
use App\Models\GradeItem;
use App\Models\GradingScale;
use App\Models\GraduationPlan;
use App\Models\GraduationRequirement;
use App\Models\StudentRecord;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Models\Weekday;
use App\Services\Exam\ExamService;
use App\Services\Syllabus\LessonNoteService;
use App\Services\Syllabus\SyllabusCoverageService;
use App\Services\Syllabus\SyllabusService;
use App\Services\Timetable\TimeSlotService;
use App\Services\Timetable\TimetableService;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Fill the teaching screens: timetables, registers, syllabi and their
 * coverage, exam windows, gradebooks, results and a graduation plan.
 *
 * Every section has a published weekly timetable, and 10A also has a draft
 * revision. Each section's register is taken for the last two weeks of
 * school. Every course offering has a published syllabus, and each class
 * moves through it at its own pace. Grade 10 keeps gradebooks: 10A's results
 * are approved, one of them in a second revision, and 10B's English results
 * wait for approval. The diploma plan reads those approved results.
 */
class TeachingRecords implements DemoPart
{
    /**
     * The bell schedule: seven class periods with lunch after the fourth.
     *
     * @var list<array{0: string, 1: string}>
     */
    private const BELLS = [
        ['08:00', '08:50'],
        ['08:55', '09:45'],
        ['09:50', '10:40'],
        ['10:45', '11:35'],
        ['12:15', '13:05'],
        ['13:10', '14:00'],
        ['14:05', '14:55'],
    ];

    private const LUNCH = ['11:35', '12:15'];

    private const STUDY_HALL = 'Study hall';

    /** @var list<string> */
    private const SCHOOL_DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    /**
     * The topics each subject teaches in a semester, in order.
     *
     * @var array<string, list<string>>
     */
    private const TOPICS = [
        'English Language Arts' => [
            'Personal narrative: telling a true story',
            'Of Mice and Men: setting and character',
            'Of Mice and Men: loneliness and dreams',
            'Literary devices: symbolism and foreshadowing',
            'Argumentative writing: claims and evidence',
            'Research skills: evaluating sources',
            'Poetry of the Harlem Renaissance',
            'To Kill a Mockingbird: point of view',
            'To Kill a Mockingbird: justice and courage',
            'Speech and debate: persuasive presentations',
        ],
        'Algebra' => [
            'Linear equations and inequalities',
            'Functions and function notation',
            'Slope and rate of change',
            'Graphing linear functions',
            'Systems of equations',
            'Exponents and exponential functions',
            'Adding and multiplying polynomials',
            'Factoring quadratics',
            'Solving quadratic equations',
            'Scatter plots and lines of fit',
        ],
        'Biology' => [
            'Lab safety and the scientific method',
            'Biomolecules',
            'Cell structure and function',
            'Cell membranes and transport',
            'Photosynthesis and cellular respiration',
            'The cell cycle and mitosis',
            'DNA structure and replication',
            'Protein synthesis',
            'Mendelian genetics',
            'Evolution and natural selection',
        ],
        'U.S. History' => [
            'Colonial America',
            'The American Revolution',
            'Writing the Constitution',
            'The early republic',
            'Westward expansion',
            'Causes of the Civil War',
            'The Civil War and Reconstruction',
            'The Industrial Revolution',
            'The Progressive Era',
            'America in World War I',
        ],
        'Spanish' => [
            'Greetings and introductions',
            'Numbers, dates, and time',
            'Family and descriptions',
            'School life and schedules',
            'Regular verbs in the present tense',
            'Food and ordering at a restaurant',
            'Ser and estar',
            'Daily routines and reflexive verbs',
            'Travel and directions',
            'The preterite tense',
        ],
        'Visual Arts' => [
            'Elements of art: line and shape',
            'Value and shading',
            'Color theory',
            'Perspective drawing',
            'Portrait proportions',
            'Printmaking',
            'Clay hand-building',
            'Art history: Impressionism',
            'Mixed media collage',
            'Portfolio and artist statement',
        ],
        'Physical Education' => [
            'Fitness testing baseline',
            'Team sports: soccer',
            'Volleyball skills',
            'Cardiovascular endurance',
            'Strength training basics',
            'Basketball fundamentals',
            'Flexibility and injury prevention',
            'Badminton',
            'Nutrition and healthy habits',
            'Personal fitness plan',
        ],
    ];

    /**
     * The grade categories every gradebook uses, with their weights.
     *
     * @var array<string, array{weight: int, aggregation: GradeAggregation}>
     */
    private const CATEGORIES = [
        'Homework' => ['weight' => 20, 'aggregation' => GradeAggregation::SimpleMean],
        'Quizzes' => ['weight' => 20, 'aggregation' => GradeAggregation::WeightedMean],
        'Projects and essays' => ['weight' => 30, 'aggregation' => GradeAggregation::WeightedMean],
        'Exams' => ['weight' => 30, 'aggregation' => GradeAggregation::WeightedMean],
    ];

    /**
     * The assessments of each subject: category, name, maximum and the week
     * it is due. "midterm" and "final" follow the exam windows.
     *
     * @var array<string, list<array{0: string, 1: string, 2: int, 3: int|string}>>
     */
    private const ASSESSMENTS = [
        'English Language Arts' => [
            ['Homework', 'Reading journal: Of Mice and Men', 20, 2],
            ['Homework', 'Vocabulary set 1', 10, 3],
            ['Quizzes', 'Literary devices quiz', 25, 5],
            ['Projects and essays', 'Personal narrative essay', 100, 6],
            ['Exams', 'Midterm exam', 100, 'midterm'],
            ['Projects and essays', 'Argumentative essay', 100, 12],
            ['Exams', 'Final exam', 100, 'final'],
        ],
        'Algebra' => [
            ['Homework', 'Problem set: linear equations', 20, 2],
            ['Homework', 'Problem set: functions', 20, 3],
            ['Quizzes', 'Slope quiz', 25, 5],
            ['Projects and essays', 'Graphing project', 50, 6],
            ['Exams', 'Midterm exam', 100, 'midterm'],
            ['Projects and essays', 'Lines of fit data project', 50, 12],
            ['Exams', 'Final exam', 100, 'final'],
        ],
        'Biology' => [
            ['Homework', 'Lab report: the scientific method', 30, 2],
            ['Homework', 'Cell diagram', 20, 3],
            ['Quizzes', 'Biomolecules quiz', 25, 5],
            ['Projects and essays', 'Osmosis lab report', 50, 6],
            ['Exams', 'Midterm exam', 100, 'midterm'],
            ['Projects and essays', 'Mitosis model project', 50, 12],
            ['Exams', 'Final exam', 100, 'final'],
        ],
        'U.S. History' => [
            ['Homework', 'Reading questions: colonial America', 20, 2],
            ['Homework', 'Primary source analysis', 20, 3],
            ['Quizzes', 'American Revolution quiz', 25, 5],
            ['Projects and essays', 'Constitution debate', 50, 6],
            ['Exams', 'Midterm exam', 100, 'midterm'],
            ['Projects and essays', 'Civil War essay', 100, 12],
            ['Exams', 'Final exam', 100, 'final'],
        ],
        'Spanish' => [
            ['Homework', 'Vocabulary practice 1', 10, 2],
            ['Homework', 'Listening practice', 20, 3],
            ['Quizzes', 'Numbers and dates quiz', 25, 5],
            ['Projects and essays', 'My family presentation', 50, 6],
            ['Exams', 'Midterm exam', 100, 'midterm'],
            ['Projects and essays', 'Restaurant role-play', 50, 12],
            ['Exams', 'Final exam', 100, 'final'],
        ],
        'Visual Arts' => [
            ['Homework', 'Sketchbook check 1', 20, 2],
            ['Homework', 'Value scale exercise', 20, 3],
            ['Quizzes', 'Color theory quiz', 25, 5],
            ['Projects and essays', 'Perspective drawing', 50, 6],
            ['Exams', 'Midterm portfolio review', 100, 'midterm'],
            ['Projects and essays', 'Self-portrait', 50, 12],
            ['Exams', 'Final portfolio review', 100, 'final'],
        ],
        'Physical Education' => [
            ['Homework', 'Fitness log: weeks 1 and 2', 10, 2],
            ['Homework', 'Soccer skills check', 20, 3],
            ['Quizzes', 'Rules of the game quiz', 25, 5],
            ['Projects and essays', 'Team strategy project', 50, 6],
            ['Exams', 'Midterm fitness assessment', 100, 'midterm'],
            ['Projects and essays', 'Personal fitness plan', 50, 12],
            ['Exams', 'Final fitness assessment', 100, 'final'],
        ],
    ];

    /**
     * The usual standing of each seat in a section, as a percentage.
     *
     * @var list<int>
     */
    private const SEAT_AVERAGES = [91, 84, 77, 88, 72];

    /**
     * US letter grades on a 4.0 scale, with the lowest percentage of each.
     *
     * @var array<string, array{points: float, from: int}>
     */
    private const LETTER_GRADES = [
        'A' => ['points' => 4.0, 'from' => 93],
        'A-' => ['points' => 3.7, 'from' => 90],
        'B+' => ['points' => 3.3, 'from' => 87],
        'B' => ['points' => 3.0, 'from' => 83],
        'B-' => ['points' => 2.7, 'from' => 80],
        'C+' => ['points' => 2.3, 'from' => 77],
        'C' => ['points' => 2.0, 'from' => 73],
        'C-' => ['points' => 1.7, 'from' => 70],
        'D+' => ['points' => 1.3, 'from' => 67],
        'D' => ['points' => 1.0, 'from' => 60],
        'F' => ['points' => 0.0, 'from' => 0],
    ];

    /** The sections whose gradebooks the demo keeps. */
    private const GRADEBOOK_SECTIONS = ['10A', '10B'];

    private DemoSchool $demo;

    private Carbon $today;

    /** @var array<string, int> weekday id by name */
    private array $weekdays = [];

    /** @var array<int, StudentRecord> enrollment by user id */
    private array $enrollments = [];

    /** @var array<string, array{start: Carbon, stop: Carbon}> exam windows by kind */
    private array $examWindows = [];

    private GradingScale $letterGrades;

    public function seed(DemoSchool $demo): void
    {
        $this->demo = $demo;
        $this->today = school_today($demo->campus);
        $this->weekdays = Weekday::query()->pluck('id', 'name')->map(fn (mixed $id): int => (int) $id)->all();
        $this->enrollments = StudentRecord::query()
            ->where('school_id', $demo->campus->id)
            ->get()
            ->keyBy('user_id')
            ->all();

        $this->createTimetables();
        $this->takeRegisters();
        $this->planExams();
        $this->writeSyllabi();
        $this->createLetterGrades();
        $this->keepGradebooks();
        $this->createGraduationPlans();
    }

    /**
     * A published weekly timetable for every section, and a draft revision
     * of 10A's that adds a Friday advisory period.
     *
     * The subjects rotate so that no two sections share a subject in the
     * same period, which keeps every teacher in one room at a time. The one
     * section without a subject in a period has study hall.
     */
    private function createTimetables(): void
    {
        $subjects = array_values($this->demo->subjects);
        $rotation = count($subjects) + 1;
        $sectionIndex = 0;
        $timetables = app(TimetableService::class);
        $publish = app(PublishTimetable::class);

        foreach ($this->demo->sections as $sectionName => $section) {
            $events = [];

            foreach (self::SCHOOL_DAYS as $dayIndex => $day) {
                foreach (self::BELLS as $bell => [$start, $stop]) {
                    $item = ($bell + $sectionIndex + 2 * $dayIndex) % $rotation;
                    $subject = $subjects[$item] ?? null;

                    $events[] = [
                        'weekday_id' => $this->weekdays[$day],
                        'start_time' => $start,
                        'stop_time' => $stop,
                        'recurrence' => 'weekly',
                        'type' => $subject === null ? 'custom' : 'subject',
                        'subject_id' => $subject?->id,
                        'title' => $subject === null ? self::STUDY_HALL : null,
                    ];
                }

                $events[] = [
                    'weekday_id' => $this->weekdays[$day],
                    'start_time' => self::LUNCH[0],
                    'stop_time' => self::LUNCH[1],
                    'recurrence' => 'weekly',
                    'type' => 'custom',
                    'title' => 'Lunch',
                ];
            }

            $timetable = $timetables->createTimetableWithEvents([
                'name' => "$sectionName weekly schedule",
                'description' => "{$this->demo->currentPeriod->name} bell schedule for $sectionName in {$section->room}.",
                'academic_cycle_section_id' => $section->id,
                'academic_period_id' => $this->demo->currentPeriod->id,
            ], $events);

            $publish->publish($timetable, $this->demo->schoolAdmin);

            if ($sectionName === '10A') {
                $this->startAdvisoryRevision($timetable);
            }

            $sectionIndex++;
        }
    }

    /**
     * Start a draft of 10A's timetable that turns the last Friday period into
     * advisory, so the list shows a published and a draft revision.
     */
    private function startAdvisoryRevision(Timetable $published): void
    {
        $draft = app(ReviseTimetable::class)->revise($published, $this->demo->schoolAdmin);
        app(TimetableService::class)->updateTimetable($draft, [
            'name' => $draft->name,
            'description' => 'Proposed change: Friday afternoons end with an advisory period with the homeroom teacher.',
        ]);

        [$start, $stop] = self::BELLS[array_key_last(self::BELLS)];
        $slot = TimetableTimeSlot::query()
            ->where('timetable_id', $draft->id)
            ->where('start_time', 'like', "$start%")
            ->where('stop_time', 'like', "$stop%")
            ->firstOrFail();
        $advisory = CustomTimetableItem::firstOrCreate([
            'school_id' => $this->demo->campus->id,
            'name' => 'Advisory',
        ]);

        app(TimeSlotService::class)->placeRecord($slot, $this->weekdays['Friday'], 'customTimetableItem', $advisory->id);
    }

    /**
     * Take the daily register for the last two weeks of school.
     *
     * Most learners are present. A few each day are late, absent, excused or
     * went home early, so the register and the reports show every state.
     */
    private function takeRegisters(): void
    {
        $days = $this->recentSchoolDays(10);
        $recordAttendance = app(RecordAttendance::class);
        $sectionIndex = 0;

        foreach ($this->demo->sections as $sectionName => $section) {
            $taker = $section->homeroomTeacher ?? $this->demo->schoolAdmin;

            foreach ($days as $dayIndex => $day) {
                $entries = [];

                foreach ($this->demo->students[$sectionName] as $seat => $student) {
                    [$status, $reason] = $this->attendanceOf($seat, $sectionIndex, $dayIndex);
                    $entries[] = [
                        'enrollment' => $this->enrollments[$student->id],
                        'status' => $status,
                        'reason' => $reason,
                    ];
                }

                $recordAttendance->recordMany($entries, $day, actor: $taker);
            }

            $sectionIndex++;
        }
    }

    /**
     * Get the attendance of one seat on one day.
     *
     * On the latest day every section shows a mix of states. On earlier days
     * a few learners are away in a fixed pattern.
     *
     * @return array{0: AttendanceStatus, 1: string|null}
     */
    private function attendanceOf(int $seat, int $sectionIndex, int $dayIndex): array
    {
        $latestDay = [
            [AttendanceStatus::Present, null],
            [AttendanceStatus::Late, 'Bus arrived late.'],
            [AttendanceStatus::Absent, null],
            [AttendanceStatus::Excused, 'Dentist appointment. Note from parent.'],
            [AttendanceStatus::Present, null],
        ];

        if ($dayIndex === 0) {
            return $latestDay[($seat + $sectionIndex) % count($latestDay)];
        }

        return match (($seat * 7 + $sectionIndex * 3 + $dayIndex * 5) % 17) {
            0 => [AttendanceStatus::Absent, null],
            4 => [AttendanceStatus::Late, 'Overslept.'],
            9 => [AttendanceStatus::Excused, 'Family event. Parent called the office.'],
            13 => [AttendanceStatus::LeftEarly, 'Went home sick after lunch.'],
            default => [AttendanceStatus::Present, null],
        };
    }

    /**
     * Get the latest school days up to today, newest first.
     *
     * A school day is a weekday inside a semester. A weekend or a break
     * between semesters has no register.
     *
     * @return list<Carbon>
     */
    private function recentSchoolDays(int $count): array
    {
        $days = [];
        $day = $this->today->copy();
        $firstDay = $this->demo->academicYear->starts_on->copy()->startOfDay();

        while (count($days) < $count && $day->greaterThanOrEqualTo($firstDay)) {
            if ($day->isWeekday() && $this->isInASemester($day)) {
                $days[] = $day->copy();
            }

            $day->subDay();
        }

        return $days;
    }

    private function isInASemester(Carbon $day): bool
    {
        foreach ($this->demo->periods as $period) {
            if ($day->betweenIncluded($period->starts_on, $period->ends_on)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A midterm and a final exam window in each semester. The windows of the
     * current semester are open for marks.
     */
    private function planExams(): void
    {
        $saveExam = app(SaveExam::class);

        foreach ($this->demo->periods as $period) {
            $season = $this->season($period);
            $windows = $this->examWindowsOf($period);

            foreach ($windows as $kind => $window) {
                $exam = $saveExam->create([
                    'name' => "$season $kind exams",
                    'description' => $kind === 'midterm'
                        ? "Midterm exams for every course in the $season semester. Regular classes resume the following Monday."
                        : "End-of-semester exams. Results go on the $season report card.",
                    'academic_period_id' => $period->id,
                    'start_date' => $window['start']->toDateString(),
                    'stop_date' => $window['stop']->toDateString(),
                ], $this->demo->schoolAdmin);

                if ($period->is($this->demo->currentPeriod)) {
                    app(ExamService::class)->setExamActiveStatus($exam, true);
                }
            }

            if ($period->is($this->demo->currentPeriod)) {
                $this->examWindows = $windows;
            }
        }
    }

    /**
     * Get the exam weeks of a semester: the ninth week, and the last week.
     *
     * @return array{midterm: array{start: Carbon, stop: Carbon}, final: array{start: Carbon, stop: Carbon}}
     */
    private function examWindowsOf(AcademicPeriod $period): array
    {
        $midterm = $period->starts_on->copy()->startOfWeek(Carbon::MONDAY)->addWeeks(8);
        $finalStop = $period->ends_on->copy();
        $finalStart = $finalStop->copy()->startOfWeek(Carbon::MONDAY);

        return [
            'midterm' => ['start' => $midterm, 'stop' => $midterm->copy()->addDays(4)],
            'final' => ['start' => $finalStart->max($period->starts_on), 'stop' => $finalStop],
        ];
    }

    /**
     * Name the season of a semester, such as "Fall".
     */
    private function season(AcademicPeriod $period): string
    {
        return strtok($period->name, ' ') ?: $period->name;
    }

    /**
     * A published syllabus for every course offering, with each class's
     * progress through it. 10A's English class also keeps weekly lesson notes
     * and has a revision of its syllabus waiting as a draft.
     */
    private function writeSyllabi(): void
    {
        $sectionIndex = array_flip(array_keys($this->demo->sections));

        foreach ($this->demo->offerings as $offering) {
            $section = $this->sectionOf($offering);
            $subject = $offering->subject->name;
            $teacher = $this->demo->teachers[$subject];
            $syllabus = $this->publishSyllabus($offering, $section, $teacher);

            $this->recordCoverage($syllabus, $teacher, $sectionIndex[$section->name]);

            if ($section->name === '10A' && $subject === 'English Language Arts') {
                $this->writeLessonNotes($syllabus, $teacher);
                app(ReviseSyllabus::class)->revise($syllabus, [
                    'change_note' => 'Adds a week of Harlem Renaissance poetry before the midterm.',
                ], $teacher);
            }
        }
    }

    private function publishSyllabus(CourseOffering $offering, AcademicCycleSection $section, User $teacher): Syllabus
    {
        $subject = $offering->subject->name;
        $syllabuses = app(SyllabusService::class);
        $syllabus = $syllabuses->createSyllabus([
            'name' => "$subject $section->name: {$this->demo->currentPeriod->name}",
            'description' => "What $section->name learns in $subject this semester, week by week.",
            'course_offering_id' => $offering->id,
        ]);
        $topics = self::TOPICS[$subject];
        $weeks = $this->teachingWeeks();

        foreach ($topics as $index => $title) {
            $syllabuses->saveTopic($syllabus, [
                'title' => $title,
                'week' => 1 + intdiv($index * $weeks, count($topics)),
                'objectives' => 'Students can explain the key ideas of '.lcfirst($title).' and use them in class work.',
                'content' => 'Direct instruction, guided practice in small groups, and an exit ticket at the end of each lesson.',
            ]);
        }

        app(SubmitSyllabus::class)->submit($syllabus, $teacher);

        return app(PublishSyllabus::class)->publish($syllabus, $this->demo->schoolAdmin);
    }

    /**
     * Count the weeks of the current semester.
     */
    private function teachingWeeks(): int
    {
        $period = $this->demo->currentPeriod;

        return max(1, intdiv((int) $period->starts_on->diffInDays($period->ends_on), 7) + 1);
    }

    /**
     * Get the teaching week today falls in, kept inside the semester.
     *
     * A demo opened in the first days of the semester still shows a few
     * covered topics.
     */
    private function currentWeek(): int
    {
        $period = $this->demo->currentPeriod;

        if ($this->today->lessThan($period->starts_on)) {
            return 3;
        }

        $week = intdiv((int) $period->starts_on->diffInDays($this->today), 7) + 1;

        return min(max($week, 3), $this->teachingWeeks() + 1);
    }

    /**
     * Record what one class was taught of its syllabus.
     *
     * Topics planned before this week are covered, except that some classes
     * run one or two topics behind. The topic of the latest finished week is
     * partly covered, and later topics are still to come.
     */
    private function recordCoverage(Syllabus $syllabus, User $teacher, int $sectionIndex): void
    {
        $coverage = app(SyllabusCoverageService::class);
        $currentWeek = $this->currentWeek();
        $lag = [0, 1, 0, 2, 1, 0, 1, 0][$sectionIndex % 8];
        $taught = $syllabus->topics()->get()
            ->filter(fn (SyllabusTopic $topic): bool => $topic->week !== null && $topic->week < $currentWeek)
            ->values();
        $taught = $taught->slice(0, max(2, $taught->count() - $lag))->values();

        foreach ($taught as $index => $topic) {
            $isLatest = $index === $taught->count() - 1;
            $status = $isLatest ? TopicCoverageStatus::Partial : TopicCoverageStatus::Covered;
            $note = $isLatest ? 'Started this topic. The practice work carries into next week.' : null;

            if ($sectionIndex === 5 && $index === 1) {
                $status = TopicCoverageStatus::Skipped;
                $note = 'Skipped for the assembly week. Folded into the next topic.';
            }

            $plannedOn = $this->demo->currentPeriod->starts_on->copy()->addWeeks($topic->week - 1)->addDays(3);

            $coverage->record(
                $syllabus,
                $topic,
                null,
                $status,
                $plannedOn->min($this->today)->toDateString(),
                $note,
                $teacher,
            );
        }
    }

    /**
     * Weekly lesson notes for 10A English: the earlier weeks are approved and
     * the latest one waits for the principal's review.
     */
    private function writeLessonNotes(Syllabus $syllabus, User $teacher): void
    {
        $notes = app(LessonNoteService::class);
        $topics = $syllabus->topics()->orderBy('position')->get();
        $lastWeek = max(1, min($this->currentWeek() - 1, 5));

        foreach (range(1, $lastWeek) as $week) {
            $topic = $topics->filter(fn (SyllabusTopic $topic): bool => $topic->week <= $week)->last() ?? $topics->first();
            $note = $notes->save($syllabus, null, [
                'week' => $week,
                'syllabus_topic_id' => $topic?->id,
                'objectives' => "Students can discuss {$this->lowerFirst($topic?->title)} with evidence from the text.",
                'activities' => 'Warm-up journal prompt, a short mini-lesson, small-group reading with discussion questions, and an exit ticket.',
                'evaluation' => 'Most students met the goal. Three need another pass at citing evidence in small groups.',
            ], $teacher);
            $notes->submit($note, $teacher);

            if ($week < $lastWeek) {
                $notes->approve($note, $this->demo->schoolAdmin);
            }
        }
    }

    private function lowerFirst(?string $text): string
    {
        return lcfirst((string) $text);
    }

    /**
     * The school's US letter grade scale on a 4.0 grade point basis.
     */
    private function createLetterGrades(): void
    {
        $options = [];

        foreach (self::LETTER_GRADES as $label => $grade) {
            $options[] = ['label' => $label, 'points' => $grade['points']];
        }

        $this->letterGrades = app(SaveGradingScale::class)->create([
            'name' => 'US letter grades',
            'description' => 'A to F letter grades on a 4.0 grade point scale.',
            'scale_type' => GradingScaleType::Gpa->value,
            'maximum_value' => 4.0,
            'options' => $options,
        ], $this->demo->schoolAdmin);
    }

    /**
     * Gradebooks for every Grade 10 course offering.
     *
     * 10A's results are sent by the teacher and approved by the principal.
     * One English result has a second revision after late work came in. 10B's
     * English results wait for approval.
     */
    private function keepGradebooks(): void
    {
        foreach ($this->demo->offerings as $offering) {
            $section = $this->sectionOf($offering);

            if (!in_array($section->name, self::GRADEBOOK_SECTIONS, true)) {
                continue;
            }

            $subject = $offering->subject->name;
            $teacher = $this->demo->teachers[$subject];
            $items = $this->setUpGradebook($offering, $teacher);
            $this->recordMarks($offering, $section, $items, $teacher);

            if ($section->name === '10A') {
                $this->sendResults($offering, $section, $teacher, approve: true);

                if ($subject === 'English Language Arts') {
                    $this->reviseLateWork($offering, $section, $items, $teacher);
                }
            } elseif ($subject === 'English Language Arts') {
                $this->sendResults($offering, $section, $teacher, approve: false);
            }
        }
    }

    /**
     * Create the categories and assessments of one gradebook.
     *
     * @return list<GradeItem>
     */
    private function setUpGradebook(CourseOffering $offering, User $teacher): array
    {
        $subject = $offering->subject->name;
        $categories = [];
        $position = 1;

        foreach (self::CATEGORIES as $name => $category) {
            $categories[$name] = GradeCategory::create([
                'school_id' => $offering->school_id,
                'course_offering_id' => $offering->id,
                'name' => $name,
                'aggregation' => $category['aggregation'],
                'weight' => $category['weight'],
                'position' => $position++,
            ]);
        }

        $items = [];
        $season = $this->season($this->demo->currentPeriod);

        foreach (self::ASSESSMENTS[$subject] as $index => [$category, $name, $maximum, $week]) {
            $dueOn = is_string($week)
                ? $this->examWindows[$week]['start']->copy()
                : $this->demo->currentPeriod->starts_on->copy()->addWeeks($week - 1)->addDays(4);
            // An assessment that is not due yet would count as nothing in
            // the result, so the gradebook holds only work already set.
            if ($dueOn->greaterThan($this->today) && $index >= 3) {
                continue;
            }

            $items[] = GradeItem::create([
                'school_id' => $offering->school_id,
                'course_offering_id' => $offering->id,
                'grade_category_id' => $categories[$category]->id,
                'name' => is_string($week) ? "$season ".lcfirst($name) : $name,
                'type' => GradeItemType::Numeric,
                'max_points' => $maximum,
                'weight' => 1,
                'due_on' => $dueOn->min($this->today)->toDateString(),
                'position' => $index + 1,
                'created_by' => $teacher->id,
            ]);
        }

        if ($subject === 'English Language Arts') {
            $participation = GradeCategory::create([
                'school_id' => $offering->school_id,
                'course_offering_id' => $offering->id,
                'name' => 'Participation',
                'aggregation' => GradeAggregation::WeightedMean,
                'weight' => 10,
                'position' => $position,
            ]);

            $items[] = GradeItem::create([
                'school_id' => $offering->school_id,
                'course_offering_id' => $offering->id,
                'grade_category_id' => $participation->id,
                'name' => 'Class participation',
                'type' => GradeItemType::Scale,
                'grading_scale_id' => $this->letterGrades->id,
                'max_points' => $this->letterGrades->maximum_value,
                'weight' => 1,
                'due_on' => $this->today->toDateString(),
                'position' => count($items) + 1,
                'created_by' => $teacher->id,
            ]);
        }

        return $items;
    }

    /**
     * Mark every assessment in the gradebook.
     *
     * A few marks carry a state instead of a number: one learner was absent
     * on the day, one never handed in work, one was excused, and one handed
     * in a draft only.
     *
     * @param  list<GradeItem>  $items
     */
    private function recordMarks(CourseOffering $offering, AcademicCycleSection $section, array $items, User $teacher): void
    {
        $recordGrade = app(RecordGrade::class);
        $subjectIndex = array_search($offering->subject->name, array_keys($this->demo->subjects), true);

        foreach ($this->demo->students[$section->name] as $seat => $student) {
            foreach ($items as $index => $item) {
                $percentage = $this->percentageOf($seat, $index, (int) $subjectIndex);
                [$state, $comment] = match ([$seat, $index]) {
                    [1, 0] => [GradeEntryState::Absent, 'Absent on the day. A make-up is offered.'],
                    [3, 1] => [GradeEntryState::Missing, 'Not handed in.'],
                    [4, 2] => [GradeEntryState::Exempt, 'Excused after a family emergency. Approved by the counselor.'],
                    [2, 3] => [GradeEntryState::Incomplete, 'Draft handed in. The final copy is still due.'],
                    default => [GradeEntryState::Graded, null],
                };

                if ($item->type === GradeItemType::Scale) {
                    $recordGrade->record($item, $this->enrollments[$student->id], GradeEntryState::Graded, gradingScaleOptionId: $this->letterOption($percentage), actor: $teacher);

                    continue;
                }

                $recordGrade->record(
                    $item,
                    $this->enrollments[$student->id],
                    $state,
                    $state === GradeEntryState::Graded ? round((float) $item->max_points * $percentage / 100) : null,
                    comment: $comment,
                    actor: $teacher,
                );
            }
        }
    }

    /**
     * Get the score of one seat on one assessment, near that seat's average.
     */
    private function percentageOf(int $seat, int $itemIndex, int $subjectIndex): int
    {
        $spread = (($itemIndex * 7 + $seat * 3 + $subjectIndex * 5) % 11) - 5;

        return min(100, max(45, self::SEAT_AVERAGES[$seat % count(self::SEAT_AVERAGES)] + $spread));
    }

    /**
     * Get the letter grade option for a percentage.
     */
    private function letterOption(int $percentage): int
    {
        foreach (self::LETTER_GRADES as $label => $grade) {
            if ($percentage >= $grade['from']) {
                return (int) $this->letterGrades->options()->where('label', $label)->value('id');
            }
        }

        return (int) $this->letterGrades->options()->where('label', 'F')->value('id');
    }

    /**
     * The teacher sends each learner's result. The principal approves them
     * when asked to.
     */
    private function sendResults(CourseOffering $offering, AcademicCycleSection $section, User $teacher, bool $approve): void
    {
        foreach ($this->demo->students[$section->name] as $student) {
            $result = app(PublishResult::class)->publish($offering, $this->enrollments[$student->id], $teacher);

            if ($approve) {
                app(ApproveResult::class)->approve($result, $this->demo->schoolAdmin, 'Checked against the mark sheet.');
            }
        }
    }

    /**
     * The learner who missed the vocabulary set hands it in late. The teacher
     * marks it and sends revision 2 of the result, which is approved.
     *
     * @param  list<GradeItem>  $items
     */
    private function reviseLateWork(CourseOffering $offering, AcademicCycleSection $section, array $items, User $teacher): void
    {
        $enrollment = $this->enrollments[$this->demo->students[$section->name][3]->id];
        $item = $items[1];

        app(RecordGrade::class)->record($item, $enrollment, GradeEntryState::Graded, round((float) $item->max_points * 0.8), comment: 'Handed in late. Accepted with a small deduction.', actor: $teacher);
        $result = app(PublishResult::class)->publish($offering, $enrollment, $teacher, 'Late vocabulary set accepted.');
        app(ApproveResult::class)->approve($result, $this->demo->schoolAdmin, 'Late work confirmed with the teacher.');
    }

    /**
     * The diploma plan, with stages for core courses, electives and health,
     * and an honors plan that asks for strong marks in three core courses.
     */
    private function createGraduationPlans(): void
    {
        $plans = app(ManageGraduationPlan::class);
        $admin = $this->demo->schoolAdmin;

        $diploma = $plans->create([
            'name' => 'Riverside High School Diploma',
            'description' => 'What every learner finishes over four years to graduate from Riverside High School.',
            'cohort_id' => null,
            'completion_operator' => 'all',
            'required_count' => null,
            'uses_credits' => true,
            'required_credits' => 18,
        ], $admin);

        $core = $plans->addStage($diploma, [
            'name' => 'Core academics',
            'description' => 'English, math, science and social studies.',
            'completion_operator' => 'all',
            'required_count' => null,
            'required_credits' => null,
            'is_negated' => false,
        ], $admin);

        foreach (['English Language Arts' => 4, 'Algebra' => 3, 'Biology' => 3, 'U.S. History' => 3] as $subject => $credits) {
            $this->requirement($core, $subject, "$subject: $credits credits", $credits);
        }

        $electives = $plans->addStage($diploma, [
            'name' => 'Arts and world languages',
            'description' => 'At least one course in the arts or a world language.',
            'completion_operator' => 'at_least',
            'required_count' => 1,
            'required_credits' => null,
            'is_negated' => false,
        ], $admin);

        $this->requirement($electives, 'Spanish', 'Spanish: 2 credits', 2);
        $this->requirement($electives, 'Visual Arts', 'Visual Arts: 1 credit', 1);

        $health = $plans->addStage($diploma, [
            'name' => 'Health and fitness',
            'description' => 'Physical education and health education.',
            'completion_operator' => 'all',
            'required_count' => null,
            'required_credits' => null,
            'is_negated' => false,
        ], $admin);

        $physicalEducation = $this->requirement($health, 'Physical Education', 'Physical Education: 1 credit', 1);
        $this->requirement($health, null, 'Health education: 1 credit', 1);

        $this->requirement($diploma, null, 'Senior capstone project', 1);
        $this->requirement($diploma, null, 'Community service: 40 hours', 0);

        $excused = $this->demo->students['10A'][4];
        $plans->excuse($physicalEducation, $this->enrollments[$excused->id], 'Medical exemption on file with the school nurse.', $admin);

        $honors = $plans->create([
            'name' => 'Honors diploma',
            'description' => 'An 85% or higher in at least three core courses.',
            'cohort_id' => null,
            'completion_operator' => 'at_least',
            'required_count' => 3,
            'uses_credits' => false,
            'required_credits' => null,
        ], $admin);

        foreach (['English Language Arts', 'Algebra', 'Biology', 'U.S. History'] as $subject) {
            $this->requirement($honors, $subject, "$subject with 85% or higher", 0, 85);
        }
    }

    /**
     * Add one requirement to a plan or stage.
     */
    private function requirement(GraduationPlan $plan, ?string $subject, string $description, int $credits, float $passMark = 60): GraduationRequirement
    {
        return app(ManageGraduationPlan::class)->addRequirement($plan, [
            'description' => $description,
            'subject_id' => $subject === null ? null : $this->demo->subjects[$subject]->id,
            'credits' => $credits,
            'pass_mark' => $passMark,
            'is_required' => true,
            'is_negated' => false,
        ], $this->demo->schoolAdmin);
    }

    /**
     * Get the section a course offering teaches.
     */
    private function sectionOf(CourseOffering $offering): AcademicCycleSection
    {
        $sectionId = (int) $offering->cycleSections()->value('academic_cycle_sections.id');

        foreach ($this->demo->sections as $section) {
            if ($section->id === $sectionId) {
                return $section;
            }
        }

        throw new RuntimeException("Course offering $offering->id teaches no demo section.");
    }
}
