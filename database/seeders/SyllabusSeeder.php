<?php

namespace Database\Seeders;

use App\Actions\Syllabus\ReviseSyllabus;
use App\Actions\Syllabus\SubmitSyllabus;
use App\Enums\AcademicPeriodStatus;
use App\Enums\LessonNoteStatus;
use App\Enums\SyllabusStatus;
use App\Enums\TopicCoverageStatus;
use App\Models\CourseOffering;
use App\Models\CurriculumOutline;
use App\Models\GradeItem;
use App\Models\LessonNote;
use App\Models\School;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\SyllabusTopicCoverage;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

/**
 * Fill a demo school with syllabi that show every part of the feature.
 *
 * Each published syllabus gets a weekly plan and class-by-class coverage.
 * A teacher gets lesson notes in each review state, two syllabi get open
 * revisions, the library gets an outline, and gradebook items test topics.
 * Running the seeder again adds nothing twice.
 */
class SyllabusSeeder extends Seeder
{
    /**
     * The weekly plan every demo syllabus follows.
     *
     * @var list<array{title: string, objectives: string, content: string, resources: string|null}>
     */
    private const TOPICS = [
        ['title' => 'Course overview and baseline check', 'objectives' => 'Students can describe what the course covers and how it is assessed.', 'content' => 'Course outline, class rules, and a short diagnostic exercise.', 'resources' => 'Course outline handout'],
        ['title' => 'Key terms and ideas', 'objectives' => 'Students can define the key terms of the subject and give an example of each.', 'content' => 'Vocabulary building, examples from daily life, and a matching exercise.', 'resources' => 'Glossary sheet'],
        ['title' => 'Core skills practice', 'objectives' => 'Students can apply the core skills to routine questions without help.', 'content' => 'Worked examples, guided practice in pairs, then independent practice.', 'resources' => null],
        ['title' => 'Applying the ideas', 'objectives' => 'Students can use the ideas to solve an unfamiliar problem.', 'content' => 'Problem-solving tasks, group discussion, and short presentations.', 'resources' => 'Textbook chapter 3'],
        ['title' => 'Practical work', 'objectives' => 'Students can plan and carry out a short practical task and record the results.', 'content' => 'Demonstration, group practical, and a written record of the task.', 'resources' => 'Practical worksheet'],
        ['title' => 'Mid-term review and test', 'objectives' => 'Students can answer test questions on weeks 1 to 5.', 'content' => 'Revision quiz, past questions, and the mid-term test.', 'resources' => null],
        ['title' => 'Extension topic', 'objectives' => 'Students can connect the course ideas to a wider question.', 'content' => 'Reading, a short research task, and a class debate.', 'resources' => 'Library reading list'],
        ['title' => 'Revision and project presentations', 'objectives' => 'Students can present a finished project and answer questions on it.', 'content' => 'Project presentations, peer feedback, and revision for the end-of-term test.', 'resources' => null],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::query()->first();

        if ($school === null) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($school->id);
        $teacher = $this->teacherOf($school);
        $syllabi = $this->publishedSyllabi($school);

        foreach ($syllabi->values() as $index => $syllabus) {
            $this->recordCoverage($syllabus, $index, $teacher);
            $this->tagAssessments($syllabus);
        }

        if ($teacher === null) {
            return;
        }

        foreach ($syllabi->take(3) as $syllabus) {
            $this->assignTeacher($syllabus->courseOffering, $teacher);
            $this->writeLessonNotes($syllabus, $teacher, $this->reviewerOf($school, $teacher));
        }

        $this->openRevisions($syllabi, $teacher);
        $this->fillLibrary($school, $syllabi->first(), $teacher);
    }

    /**
     * Publish a syllabus with a weekly plan for up to ten open course offerings.
     *
     * @return Collection<int, Syllabus>
     */
    private function publishedSyllabi(School $school): Collection
    {
        $courseOfferings = CourseOffering::query()
            ->where('school_id', $school->id)
            ->whereHas('academicPeriod', function (Builder $query): void {
                $query->whereNotIn('status', [
                    AcademicPeriodStatus::Closed->value,
                    AcademicPeriodStatus::Archived->value,
                ]);
            })
            ->orderBy('id')
            ->limit(10)
            ->get();

        $syllabi = new Collection;

        foreach ($courseOfferings->values() as $index => $courseOffering) {
            $filePath = 'pdfs/demo-syllabus-'.$courseOffering->id.'.pdf';
            Storage::disk('public')->put($filePath, "%PDF-1.4\nDemo syllabus\n");

            $syllabus = Syllabus::query()->firstOrCreate(
                ['course_offering_id' => $courseOffering->id, 'status' => SyllabusStatus::Published],
                [
                    'name' => 'Demo syllabus '.($index + 1),
                    'description' => 'Course plan and learning outcomes for the simulated school.',
                    'file' => $filePath,
                    'published_at' => now(),
                ],
            );

            if (!$syllabus->topics()->exists()) {
                foreach (self::TOPICS as $position => $topic) {
                    $syllabus->topics()->create(['week' => $position + 1, 'position' => $position + 1, ...$topic]);
                }
            }

            $syllabi->push($syllabus->load('courseOffering.academicLevel', 'topics'));
        }

        return $syllabi;
    }

    /**
     * Mark how far each class got, so the classes move at different speeds.
     */
    private function recordCoverage(Syllabus $syllabus, int $index, ?User $teacher): void
    {
        $topics = $syllabus->topics->values();

        if (SyllabusTopicCoverage::query()->whereIn('syllabus_topic_id', $topics->modelKeys())->exists()) {
            return;
        }

        foreach (app(SyllabusCoverageService::class)->tracks($syllabus) as $trackIndex => $track) {
            $coveredWeeks = ($index + $trackIndex) % 5 + 1;

            foreach ($topics->take($coveredWeeks + 1) as $position => $topic) {
                $status = match (true) {
                    $position === $coveredWeeks => TopicCoverageStatus::Partial,
                    $position === 1 && $index % 3 === 2 => TopicCoverageStatus::Skipped,
                    default => TopicCoverageStatus::Covered,
                };

                SyllabusTopicCoverage::query()->create([
                    'syllabus_topic_id' => $topic->id,
                    'academic_cycle_section_id' => $track['id'],
                    'status' => $status,
                    'covered_on' => $status === TopicCoverageStatus::Skipped ? null : now()->subWeeks($coveredWeeks - $position)->toDateString(),
                    'note' => $status === TopicCoverageStatus::Partial ? 'Ran out of time. Finish the practice questions next lesson.' : null,
                    'recorded_by' => $teacher?->id,
                ]);
            }
        }
    }

    /**
     * Link the offering's first gradebook items to the topics they test.
     */
    private function tagAssessments(Syllabus $syllabus): void
    {
        $topics = $syllabus->topics->values();
        $gradeItems = GradeItem::query()->where('course_offering_id', $syllabus->course_offering_id)->orderBy('id')->limit(2)->get();

        foreach ($gradeItems->values() as $index => $gradeItem) {
            $tested = $index === 0 ? $topics->slice(0, 3) : $topics->slice(3, 3);
            $gradeItem->syllabusTopics()->syncWithoutDetaching($tested->modelKeys());
        }
    }

    /**
     * Let the demo teacher teach the course offering, so they can record its lessons.
     */
    private function assignTeacher(CourseOffering $courseOffering, User $teacher): void
    {
        TeachingAssignment::query()->firstOrCreate(
            ['course_offering_id' => $courseOffering->id, 'user_id' => $teacher->id],
            [
                'school_id' => $courseOffering->school_id,
                'subject_id' => $courseOffering->subject_id,
                'academic_year_id' => $courseOffering->academic_year_id,
                'academic_period_id' => $courseOffering->academic_period_id,
                'starts_on' => now()->subMonths(2)->toDateString(),
            ],
        );
    }

    /**
     * Write one lesson note in each review state for the first class.
     */
    private function writeLessonNotes(Syllabus $syllabus, User $teacher, ?User $reviewer): void
    {
        if (LessonNote::query()->where('course_offering_id', $syllabus->course_offering_id)->exists()) {
            return;
        }

        $sectionId = app(SyllabusCoverageService::class)->tracks($syllabus)[0]['id'];
        $topics = $syllabus->topics->values();
        $notes = [
            ['status' => LessonNoteStatus::Approved, 'review_note' => 'Good plan. Keep the pair work.'],
            ['status' => LessonNoteStatus::Returned, 'review_note' => 'Add how you will check that students understood the task.'],
            ['status' => LessonNoteStatus::Submitted, 'review_note' => null],
            ['status' => LessonNoteStatus::Draft, 'review_note' => null],
        ];

        foreach ($notes as $position => $note) {
            $topic = $topics->get($position);
            $isReviewed = in_array($note['status'], [LessonNoteStatus::Approved, LessonNoteStatus::Returned], true);

            LessonNote::query()->create([
                'course_offering_id' => $syllabus->course_offering_id,
                'syllabus_topic_id' => $topic?->id,
                'academic_cycle_section_id' => $sectionId,
                'user_id' => $teacher->id,
                'week' => $position + 1,
                'objectives' => $topic->objectives ?? 'Students can explain the ideas of the week.',
                'activities' => 'Starter question, teacher demonstration, guided practice in pairs, then a short exit task.',
                'evaluation' => $note['status'] === LessonNoteStatus::Returned ? null : 'Exit task marked in class. Students who miss two questions get help next lesson.',
                'status' => $note['status'],
                'submitted_at' => $note['status'] === LessonNoteStatus::Draft ? null : now()->subDays(10 - $position * 2),
                'reviewed_by' => $isReviewed ? $reviewer?->id : null,
                'reviewed_at' => $isReviewed ? now()->subDays(9 - $position * 2) : null,
                'review_note' => $note['review_note'],
            ]);
        }
    }

    /**
     * Open two revisions: one waits for review, one was sent back.
     *
     * @param  Collection<int, Syllabus>  $syllabi
     */
    private function openRevisions(Collection $syllabi, User $teacher): void
    {
        $forReview = $syllabi->get(1);
        $sentBack = $syllabi->get(2);

        if ($forReview !== null && $forReview->openRevision() === null) {
            $revision = app(ReviseSyllabus::class)->revise($forReview, ['change_note' => 'Move the practical work before the mid-term test.'], $teacher);
            app(SubmitSyllabus::class)->submit($revision, $teacher);
        }

        if ($sentBack !== null && $sentBack->openRevision() === null) {
            $revision = app(ReviseSyllabus::class)->revise($sentBack, ['change_note' => 'Add a week on exam technique.'], $teacher);
            $revision->update(['review_note' => 'Say which past papers the exam technique week uses.']);
        }
    }

    /**
     * Keep the first syllabus's plan in the library for reuse.
     */
    private function fillLibrary(School $school, ?Syllabus $syllabus, User $teacher): void
    {
        if ($syllabus === null) {
            return;
        }

        $courseOffering = $syllabus->courseOffering;
        $outline = CurriculumOutline::query()->firstOrCreate(
            ['school_id' => $school->id, 'subject_id' => $courseOffering->subject_id, 'name' => 'Standard '.$courseOffering->academicLevel->name.' scheme of work'],
            [
                'academic_level_id' => $courseOffering->academic_level_id,
                'description' => 'The department plan for one term. Copy it into a new draft and adjust the weeks.',
                'created_by' => $teacher->id,
            ],
        );

        if ($outline->topics()->exists()) {
            return;
        }

        $outline->topics()->createMany($syllabus->topics->map(fn (SyllabusTopic $topic): array => $topic->only(['week', 'position', 'title', 'objectives', 'content', 'resources']))->all());
    }

    /**
     * Get the school's first teacher.
     */
    private function teacherOf(School $school): ?User
    {
        return User::query()
            ->whereHas('schools', fn (Builder $query): Builder => $query->whereKey($school->id))
            ->whereHas('teacherRecord')
            ->orderBy('id')
            ->first();
    }

    /**
     * Get someone in the school, other than the teacher, who reviews syllabi.
     */
    private function reviewerOf(School $school, User $teacher): ?User
    {
        return User::query()
            ->whereHas('schools', fn (Builder $query): Builder => $query->whereKey($school->id))
            ->whereKeyNot($teacher->id)
            ->orderBy('id')
            ->get()
            ->first(fn (User $user): bool => $user->can('approve syllabus'));
    }
}
