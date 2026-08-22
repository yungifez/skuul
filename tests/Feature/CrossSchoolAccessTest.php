<?php

namespace Tests\Feature;

use App\Actions\School\GrantSchoolMembership;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\CourseOffering;
use App\Models\CustomTimetableItem;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use App\Models\GradeSystem;
use App\Models\MyClass;
use App\Models\Notice;
use App\Models\Promotion;
use App\Models\School;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\Timetable;
use App\Models\TimetableTimeSlot;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * A person may only reach the records of the school they work in.
 *
 * Every resource keeps one record in a second school. A user who holds every
 * permission in the working school must still be refused that record.
 */
class CrossSchoolAccessTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private School $otherSchool;

    /**
     * The records that belong to the other school.
     *
     * @var array<string, Model>
     */
    private array $records = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->buildOtherSchool();
    }

    /**
     * Read, edit, update, and delete are all refused for another school.
     *
     * @return array<string, array{0: string, 1: string, 2: array<int, string>}>
     */
    public static function schoolOwnedResources(): array
    {
        return [
            'academic year' => ['academicYear', 'dashboard/academic-years/%d', ['academic year']],
            'class group' => ['classGroup', 'dashboard/class-groups/%d', ['class group']],
            'class' => ['myClass', 'dashboard/classes/%d', ['class']],
            'section' => ['section', 'dashboard/sections/%d', ['section']],
            'academic period' => ['academicPeriod', 'dashboard/academic-periods/%d', ['academic period']],
            'subject' => ['subject', 'dashboard/subjects/%d', ['subject']],
            'syllabus' => ['syllabus', 'dashboard/syllabi/%d', ['syllabus']],
            'exam' => ['exam', 'dashboard/exams/%d', ['exam']],
            'grade system' => ['gradeSystem', 'dashboard/grade-systems/%d', ['grade system']],
            'notice' => ['notice', 'dashboard/notices/%d', ['notice']],
            'custom timetable item' => ['customTimetableItem', 'dashboard/custom-timetable-items/%d', ['custom timetable item']],
            'fee category' => ['feeCategory', 'dashboard/fees/fee-categories/%d', ['fee category']],
            'fee' => ['fee', 'dashboard/fees/%d', ['fee']],
            'fee invoice' => ['feeInvoice', 'dashboard/fees/fee-invoices/%d', ['fee invoice']],
            'timetable' => ['timetable', 'dashboard/timetables/%d', ['timetable']],
        ];
    }

    /**
     * @param  array<int, string>  $subjects
     */
    #[DataProvider('schoolOwnedResources')]
    public function test_a_record_of_another_school_cannot_be_read(string $key, string $uri, array $subjects): void
    {
        $this->actAsFullyPermittedUser($subjects)
            ->get($this->uriFor($uri, $key))
            ->assertForbidden();
    }

    /**
     * @param  array<int, string>  $subjects
     */
    #[DataProvider('schoolOwnedResources')]
    public function test_a_record_of_another_school_cannot_be_edited(string $key, string $uri, array $subjects): void
    {
        $this->actAsFullyPermittedUser($subjects)
            ->get($this->uriFor($uri, $key).'/edit')
            ->assertForbidden();
    }

    /**
     * @param  array<int, string>  $subjects
     */
    #[DataProvider('schoolOwnedResources')]
    public function test_a_record_of_another_school_cannot_be_updated(string $key, string $uri, array $subjects): void
    {
        $this->actAsFullyPermittedUser($subjects)
            ->put($this->uriFor($uri, $key), [])
            ->assertForbidden();
    }

    /**
     * @param  array<int, string>  $subjects
     */
    #[DataProvider('schoolOwnedResources')]
    public function test_a_record_of_another_school_cannot_be_deleted(string $key, string $uri, array $subjects): void
    {
        $this->actAsFullyPermittedUser($subjects)
            ->delete($this->uriFor($uri, $key))
            ->assertForbidden();

        $this->assertDatabaseHas(
            $this->records[$key]->getTable(),
            ['id' => $this->records[$key]->id]
        );
    }

    public function test_an_exam_slot_of_another_school_cannot_be_read(): void
    {
        $exam = $this->records['exam'];
        $slot = $this->records['examSlot'];

        $this->actAsFullyPermittedUser(['exam', 'exam slot'])
            ->get("dashboard/exams/$exam->id/manage/exam-slots/$slot->id")
            ->assertForbidden();
    }

    public function test_a_timetable_time_slot_of_another_school_cannot_be_read(): void
    {
        $slot = $this->records['timetableTimeSlot'];

        $this->actAsFullyPermittedUser(['timetable'])
            ->get("dashboard/timetables/manage/time-slots/$slot->id")
            ->assertForbidden();
    }

    public function test_a_promotion_of_another_school_cannot_be_read(): void
    {
        $promotion = $this->records['promotion'];

        $this->authorized_user(['read promotion'])
            ->get("dashboard/students/promotions/$promotion->id")
            ->assertForbidden();
    }

    public function test_a_student_of_another_school_cannot_be_reached(): void
    {
        $student = $this->records['student'];

        // The role is held per school, so the person is not a student here.
        $this->actAsFullyPermittedUser(['student'])
            ->get("dashboard/students/$student->id")
            ->assertNotFound();
    }

    public function test_an_administrator_of_another_school_cannot_be_reached(): void
    {
        $admin = $this->records['otherAdmin'];

        $this->actAsFullyPermittedUser(['admin'])
            ->get("dashboard/admins/$admin->id")
            ->assertForbidden();
    }

    public function test_the_index_screens_only_count_the_working_school(): void
    {
        $this->assertSame(1, AcademicYear::inSchool($this->otherSchool)->count());
        $this->assertFalse(
            AcademicYear::inSchool(School::first())->get()->contains($this->records['academicYear'])
        );
    }

    /**
     * Build the address of a record that belongs to the other school.
     */
    private function uriFor(string $uri, string $key): string
    {
        return sprintf($uri, $this->records[$key]->id);
    }

    /**
     * Sign in as a person who holds every permission for the given subjects.
     *
     * @param  array<int, string>  $subjects
     */
    private function actAsFullyPermittedUser(array $subjects): object
    {
        $permissions = [];

        foreach ($subjects as $subject) {
            foreach (['create', 'read', 'update', 'delete'] as $action) {
                $permissions[] = "$action $subject";
            }
        }

        return $this->authorized_user($permissions);
    }

    /**
     * Create one record of every resource inside a second school.
     */
    private function buildOtherSchool(): void
    {
        $home = $this->workingSchool();
        $this->otherSchool = School::factory()->create();

        $classGroup = ClassGroup::factory()->create(['school_id' => $this->otherSchool->id]);
        $myClass = MyClass::factory()->create(['class_group_id' => $classGroup->id]);
        $section = Section::factory()->create(['my_class_id' => $myClass->id]);
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->otherSchool->id]);
        $academicPeriod = AcademicPeriod::factory()->create([
            'school_id' => $this->otherSchool->id,
            'academic_year_id' => $academicYear->id,
        ]);
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $this->otherSchool->id]);
        $cycleSection = AcademicCycleSection::factory()->create([
            'school_id' => $this->otherSchool->id,
            'academic_year_id' => $academicYear->id,
            'academic_level_id' => $academicLevel->id,
        ]);
        $subject = Subject::factory()->create([
            'school_id' => $this->otherSchool->id,
        ]);
        $courseOffering = CourseOffering::factory()->create([
            'school_id' => $this->otherSchool->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ]);
        $exam = Exam::factory()->create(['academic_period_id' => $academicPeriod->id]);
        $feeCategory = FeeCategory::factory()->create(['school_id' => $this->otherSchool->id]);
        $timetable = Timetable::factory()->create([
            'academic_cycle_section_id' => $cycleSection->id,
            'academic_period_id' => $academicPeriod->id,
        ]);

        $student = $this->studentOfOtherSchool($cycleSection);

        $this->records = [
            'classGroup' => $classGroup,
            'myClass' => $myClass,
            'section' => $section,
            'academicYear' => $academicYear,
            'academicPeriod' => $academicPeriod,
            'cycleSection' => $cycleSection,
            'subject' => $subject,
            'exam' => $exam,
            'feeCategory' => $feeCategory,
            'timetable' => $timetable,
            'student' => $student,
            'otherAdmin' => $this->adminOfOtherSchool(),
            'syllabus' => Syllabus::factory()->create([
                'course_offering_id' => $courseOffering->id,
            ]),
            'examSlot' => ExamSlot::factory()->create(['exam_id' => $exam->id]),
            'gradeSystem' => GradeSystem::factory()->create(['class_group_id' => $classGroup->id]),
            'notice' => Notice::factory()->create(['school_id' => $this->otherSchool->id]),
            'customTimetableItem' => CustomTimetableItem::factory()->create(['school_id' => $this->otherSchool->id]),
            'fee' => Fee::factory()->create(['fee_Category_id' => $feeCategory->id]),
            'feeInvoice' => FeeInvoice::factory()->create(['user_id' => $student->id]),
            'timetableTimeSlot' => TimetableTimeSlot::factory()->create(['timetable_id' => $timetable->id]),
            'promotion' => Promotion::factory()->create([
                'school_id' => $this->otherSchool->id,
                'academic_year_id' => $academicYear->id,
                'source_academic_cycle_section_id' => $cycleSection->id,
                'destination_academic_cycle_section_id' => $cycleSection->id,
                'students' => [$student->id],
            ]),
        ];

        school_context()->set($home, remember: false);
    }

    /**
     * Create a student who belongs only to the other school.
     */
    private function studentOfOtherSchool(AcademicCycleSection $cycleSection): User
    {
        $student = $this->personOfOtherSchool('student');

        StudentRecord::factory()->create([
            'user_id' => $student->id,
            'school_id' => $this->otherSchool->id,
            'academic_cycle_section_id' => $cycleSection->id,
        ]);

        return $student;
    }

    /**
     * Create an administrator who belongs only to the other school.
     */
    private function adminOfOtherSchool(): User
    {
        return $this->personOfOtherSchool('admin');
    }

    /**
     * Create a person who holds one role in the other school only.
     */
    private function personOfOtherSchool(string $role): User
    {
        $person = $this->nonMember();

        app(GrantSchoolMembership::class)->grant($person, $this->otherSchool, primary: true);

        // Roles are held per school, so name the school before assigning one.
        school_context()->set($this->otherSchool, remember: false);
        $person->assignRole($role);

        return $person->refresh();
    }
}
