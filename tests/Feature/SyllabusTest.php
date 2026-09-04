<?php

namespace Tests\Feature;

use App\Actions\Syllabus\PublishSyllabus;
use App\Actions\Syllabus\ReviseSyllabus;
use App\Enums\AuditAction;
use App\Enums\CourseOfferingStatus;
use App\Enums\RosterMode;
use App\Enums\SyllabusStatus;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Traits\FeatureTestTrait;
use Database\Seeders\SyllabusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SyllabusTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_unauthorized_user_cant_view_all_syllabi(): void
    {
        $this->unauthorized_user()
            ->get('/dashboard/syllabi')
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_all_syllabi(): void
    {
        $this->authorized_user(['read syllabus'])
            ->get('/dashboard/syllabi')
            ->assertOk();
    }

    public function test_a_parent_cannot_open_the_staff_syllabus_workspace(): void
    {
        $this->authorized_user(['read syllabus']);
        auth()->user()->assignRole('parent');

        $this->get('/dashboard/syllabi')->assertForbidden();
    }

    public function test_a_student_only_sees_published_syllabi_for_their_active_offerings(): void
    {
        $school = $this->workingSchool();
        $studentRecord = StudentRecord::factory()->create(['school_id' => $school->id]);
        $student = $studentRecord->user;
        $student->assignRole('student');
        $student->givePermissionTo('read syllabus');

        $visibleOffering = $this->courseOffering();
        $visibleOffering->roster_mode = RosterMode::IndividualRoster;
        $visibleOffering->status = CourseOfferingStatus::Active;
        $visibleOffering->save();
        $visibleOffering->studentRecords()->attach($studentRecord);
        $visibleSyllabus = Syllabus::factory()->create(['course_offering_id' => $visibleOffering->id]);
        $visibleSyllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now()]);

        $outsideOffering = $this->courseOffering();
        $outsideOffering->roster_mode = RosterMode::IndividualRoster;
        $outsideOffering->status = CourseOfferingStatus::Active;
        $outsideOffering->save();
        $outsideSyllabus = Syllabus::factory()->create(['course_offering_id' => $outsideOffering->id]);
        $outsideSyllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now()]);

        $draftSyllabus = Syllabus::factory()->create(['course_offering_id' => $visibleOffering->id]);

        $this->actingAsMemberOf($school, $student)
            ->get('/dashboard/syllabi')
            ->assertOk()
            ->assertSee($visibleSyllabus->name)
            ->assertDontSee($outsideSyllabus->name)
            ->assertDontSee($draftSyllabus->name);
    }

    public function test_unauthorized_user_cant_view_create_syllabus(): void
    {
        $this->unauthorized_user()
            ->get('/dashboard/syllabi/create')
            ->assertForbidden();
    }

    public function test_user_can_view_create_syllabus(): void
    {
        $this->authorized_user(['create syllabus'])
            ->get('/dashboard/syllabi/create')
            ->assertOk();
    }

    public function test_the_syllabus_demo_seeder_creates_usable_records(): void
    {
        CourseOffering::factory()->count(10)->create();

        $this->seed(SyllabusSeeder::class);
        $this->seed(SyllabusSeeder::class);

        $this->assertCount(10, Syllabus::query()->get());
        $this->assertTrue(Syllabus::query()->whereNotNull('file')->exists());
    }

    public function test_unauthorized_user_cant_create_syllabus(): void
    {
        Storage::fake('public');
        $courseOffering = $this->courseOffering();

        $this->unauthorized_user()
            ->post('/dashboard/syllabi', [
                'name' => 'Test syllabus',
                'course_offering_id' => $courseOffering->id,
                'description' => 'Test syllabus description',
                'file' => UploadedFile::fake()->create('test-syllabus.pdf', 100),
            ])->assertForbidden();
    }

    public function test_authorized_user_can_create_syllabus(): void
    {
        Storage::fake('public');
        $courseOffering = $this->courseOffering();

        $this->authorized_user(['create syllabus'])
            ->post('/dashboard/syllabi', [
                'name' => 'Test syllabus',
                'course_offering_id' => $courseOffering->id,
                'description' => 'Test syllabus description',
                'file' => UploadedFile::fake()->create('test-syllabus.pdf', 100),
            ])->assertRedirect(route('syllabi.index'));

        $this->assertDatabaseHas('syllabi', [
            'name' => 'Test syllabus',
            'course_offering_id' => $courseOffering->id,
            'description' => 'Test syllabus description',
        ]);
    }

    public function test_unauthorized_user_cant_delete_syllabus(): void
    {
        $syllabus = Syllabus::factory()->create();
        $this->unauthorized_user()
            ->delete('/dashboard/syllabi/'.$syllabus->id)
            ->assertForbidden();
    }

    public function test_published_syllabus_is_revised_and_superseded_instead_of_deleted(): void
    {
        $syllabus = Syllabus::factory()->create(['course_offering_id' => $this->courseOffering()->id]);
        $syllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now()]);
        $this->authorized_user(['update syllabus']);
        $actor = auth()->user();

        $revision = app(ReviseSyllabus::class)->revise($syllabus, ['name' => 'Corrected syllabus'], $actor);

        $this->assertSame(SyllabusStatus::Draft, $revision->status);
        $this->assertSame($syllabus->id, $revision->revision_of_id);
        $this->assertSame(2, $revision->revision);

        app(PublishSyllabus::class)->publish($revision, $actor);

        $this->assertSame(SyllabusStatus::Superseded, $syllabus->fresh()->status);
        $this->assertSame(SyllabusStatus::Published, $revision->fresh()->status);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SyllabusRevised)->forSubject($revision)->first());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::SyllabusPublished)->forSubject($revision)->first());
    }

    private function courseOffering(): CourseOffering
    {
        $school = $this->workingSchool();
        $academicYear = AcademicYear::query()->findOrFail(
            AcademicYear::factory()->create(['school_id' => $school->id])->getKey(),
        );
        $academicPeriod = AcademicPeriod::query()->findOrFail(AcademicPeriod::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
        ])->getKey());
        $academicLevel = AcademicLevel::query()->findOrFail(
            AcademicLevel::factory()->create(['school_id' => $school->id])->getKey(),
        );
        $subject = Subject::query()->findOrFail(
            Subject::factory()->create(['school_id' => $school->id])->getKey(),
        );

        return CourseOffering::query()->findOrFail(CourseOffering::factory()->create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'academic_period_id' => $academicPeriod->id,
            'academic_level_id' => $academicLevel->id,
            'subject_id' => $subject->id,
        ])->getKey());
    }
}
