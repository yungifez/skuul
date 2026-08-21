<?php

namespace Tests\Feature;

use App\Actions\Academic\ChangeAcademicPeriodStatus;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Actions\Identity\ChangeAccountStatus;
use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Models\AcademicYear;
use App\Models\AuditEvent;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Exam\ExamService;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Sensitive actions leave a record that cannot be changed later.
 */
class AuditEventTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_giving_a_role_is_recorded(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();

        $user->assignRole(Role::Teacher->value);

        $event = AuditEvent::ofAction(AuditAction::RoleAttached)->latest('id')->first();

        $this->assertNotNull($event);
        $this->assertSame($user->id, $event->subject_id);
        $this->assertSame([Role::Teacher->value], $event->context['roles']);
    }

    public function test_removing_a_role_is_recorded(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();
        $user->assignRole(Role::Teacher->value);

        $user->removeRole(Role::Teacher->value);

        $event = AuditEvent::ofAction(AuditAction::RoleDetached)->latest('id')->first();

        $this->assertNotNull($event);
        $this->assertSame([Role::Teacher->value], $event->context['roles']);
    }

    public function test_giving_a_permission_is_recorded(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();

        $user->givePermissionTo('create student');

        $event = AuditEvent::ofAction(AuditAction::PermissionAttached)->latest('id')->first();

        $this->assertNotNull($event);
        $this->assertContains('create student', $event->context['permissions']);
    }

    public function test_removing_a_permission_is_recorded(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();
        $user->givePermissionTo('create student');

        $user->revokePermissionTo('create student');

        $event = AuditEvent::ofAction(AuditAction::PermissionDetached)->latest('id')->first();

        $this->assertNotNull($event);
        $this->assertContains('create student', $event->context['permissions']);
    }

    public function test_an_account_status_change_records_the_actor_and_the_reason(): void
    {
        $this->authorized_user([]);
        $actor = auth()->user();
        $user = User::factory()->create();

        app(ChangeAccountStatus::class)->changeTo($user, AccountStatus::Suspended, $actor, 'Under review');

        $event = AuditEvent::ofAction(AuditAction::AccountStatusChanged)->forSubject($user)->firstOrFail();

        $this->assertSame($actor->id, $event->actor_id);
        $this->assertSame(AccountStatus::Suspended->value, $event->context['to']);
        $this->assertSame('Under review', $event->context['reason']);
    }

    public function test_an_enrollment_status_change_is_recorded(): void
    {
        $this->authorized_user([]);
        $enrollment = StudentRecord::factory()->create();

        app(ChangeEnrollmentStatus::class)->graduate($enrollment, reason: 'Finished the program');

        $event = AuditEvent::ofAction(AuditAction::EnrollmentStatusChanged)->forSubject($enrollment)->firstOrFail();

        $this->assertSame(EnrollmentStatus::Graduated->value, $event->context['to']);
    }

    public function test_closing_an_academic_year_is_recorded(): void
    {
        $this->authorized_user([]);
        $year = AcademicYear::factory()->create();

        app(ChangeAcademicPeriodStatus::class)->close($year, reason: 'Year finished');

        $event = AuditEvent::ofAction(AuditAction::AcademicPeriodStatusChanged)->forSubject($year)->firstOrFail();

        $this->assertSame('closed', $event->context['to']);
    }

    public function test_publishing_exam_results_is_recorded(): void
    {
        $this->authorized_user([]);
        $exam = Exam::factory()->create(['publish_result' => false]);
        ExamSlot::factory()->create(['exam_id' => $exam->id]);

        app(ExamService::class)->setPublishResultStatus($exam, true);

        $event = AuditEvent::ofAction(AuditAction::ExamResultPublished)->forSubject($exam)->firstOrFail();

        $this->assertSame($exam->name, $event->context['exam']);
    }

    public function test_hiding_exam_results_again_is_recorded(): void
    {
        $this->authorized_user([]);
        $exam = Exam::factory()->create(['publish_result' => true]);
        ExamSlot::factory()->create(['exam_id' => $exam->id]);

        app(ExamService::class)->setPublishResultStatus($exam, false);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::ExamResultUnpublished)->forSubject($exam)->first());
    }

    public function test_repeating_a_publication_records_nothing_new(): void
    {
        $this->authorized_user([]);
        $exam = Exam::factory()->create(['publish_result' => true]);
        ExamSlot::factory()->create(['exam_id' => $exam->id]);

        app(ExamService::class)->setPublishResultStatus($exam, true);

        $this->assertSame(0, AuditEvent::ofAction(AuditAction::ExamResultPublished)->forSubject($exam)->count());
    }

    public function test_a_record_belongs_to_the_school_it_was_made_in(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user([]);
        $user = User::factory()->create();

        $user->assignRole(Role::Teacher->value);

        $this->assertSame($school->id, AuditEvent::inSchool($school)->latest('id')->firstOrFail()->school_id);
    }

    public function test_another_school_cannot_see_the_records(): void
    {
        $other = School::factory()->create();
        $this->authorized_user([]);
        User::factory()->create()->assignRole(Role::Teacher->value);

        $this->assertSame(0, AuditEvent::inSchool($other)->count());
    }

    public function test_a_record_cannot_be_changed(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();
        $user->assignRole(Role::Teacher->value);
        $event = AuditEvent::latest('id')->firstOrFail();

        $this->expectException(RuntimeException::class);

        $event->update(['action' => AuditAction::RoleDetached]);
    }

    public function test_a_record_cannot_be_deleted(): void
    {
        $this->authorized_user([]);
        $user = User::factory()->create();
        $user->assignRole(Role::Teacher->value);
        $event = AuditEvent::latest('id')->firstOrFail();

        $this->expectException(RuntimeException::class);

        $event->delete();
    }
}
