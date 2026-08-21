<?php

namespace Tests\Feature;

use App\Actions\Portal\SubmitPortalRequest;
use App\Enums\AttendanceStatus;
use App\Enums\Feature;
use App\Enums\PortalArea;
use App\Enums\PortalRequestStatus;
use App\Enums\PortalRequestType;
use App\Enums\TimetableStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AttendanceRecord;
use App\Models\PortalRequest;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use App\Services\Portal\PortalAccess;
use App\Services\Portal\PortalSummary;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * The portal reads what the school published, for the student and for the
 * guardians of that student, and for nobody else.
 */
class PortalTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_student_reads_their_own_enrollment(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();

        $this->assertTrue(app(PortalAccess::class)->canRead($enrollment->user, $enrollment));
    }

    public function test_a_guardian_reads_the_enrollment_of_their_child(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);

        $access = app(PortalAccess::class);

        $this->assertTrue($access->canRead($guardian, $enrollment));
        $this->assertSame([$enrollment->id], $access->enrollmentsFor($guardian)->pluck('id')->all());
    }

    public function test_a_guardian_with_two_children_reads_both(): void
    {
        $this->unauthorized_user();
        $first = $this->enrollment();
        $second = $this->enrollment();
        $guardian = $this->guardianOf($first);
        $guardian->parentRecord->students()->syncWithoutDetaching($second->user);

        $enrollments = app(PortalAccess::class)->enrollmentsFor($guardian->fresh());

        $this->assertSame([$first->id, $second->id], $enrollments->pluck('id')->sort()->values()->all());
    }

    public function test_a_stranger_reads_nothing(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $access = app(PortalAccess::class);

        $this->assertFalse($access->canRead($stranger, $enrollment));
        $this->assertSame([], $access->enrollmentsFor($stranger)->pluck('id')->all());
    }

    public function test_a_closed_portal_reads_nothing(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        features()->disable(Feature::Portal);

        $this->assertFalse(app(PortalAccess::class)->canRead($enrollment->user, $enrollment));
    }

    public function test_a_school_closes_one_area_and_keeps_the_rest(): void
    {
        $this->unauthorized_user();
        features()->enable(Feature::Portal, config: [PortalArea::Invoices->value => false]);

        $access = app(PortalAccess::class);

        $this->assertFalse($access->areaIsOpen(PortalArea::Invoices));
        $this->assertTrue($access->areaIsOpen(PortalArea::Results));
    }

    public function test_only_published_results_reach_the_family(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->publishedResult($enrollment, $subject, 55);
        $this->publishedResult($enrollment, $subject, 71, revision: 2);

        $results = app(PortalSummary::class)->results($enrollment);

        $this->assertSame(1, $results->count());
        $this->assertSame(71.0, $results->first()->percentage);
    }

    public function test_a_closed_results_area_shows_no_results(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $subject = Subject::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->publishedResult($enrollment, $subject, 71);
        features()->enable(Feature::Portal, config: [PortalArea::Results->value => false]);

        $this->assertTrue(app(PortalSummary::class)->results($enrollment)->isEmpty());
    }

    public function test_the_family_sees_the_attendance_rate(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        AttendanceRecord::create([
            'school_id'         => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'academic_year_id'  => current_academic_year_id(),
            'semester_id'       => current_semester_id(),
            'attended_on'       => now()->subDay()->toDateString(),
            'status'            => AttendanceStatus::Present,
        ]);

        $attendance = app(PortalSummary::class)->attendance($enrollment);

        $this->assertSame(1, $attendance['recorded']);
        $this->assertSame(100.0, $attendance['rate']);
    }

    public function test_only_a_published_timetable_reaches_the_family(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $draft = Timetable::factory()->create([
            'my_class_id' => $enrollment->my_class_id,
            'section_id'  => $enrollment->section_id,
        ]);

        $summary = app(PortalSummary::class);

        $this->assertNull($summary->timetable($enrollment));

        $draft->forceFill(['status' => TimetableStatus::Published, 'published_at' => now()])->save();

        $this->assertSame($draft->id, $summary->timetable($enrollment)?->id);
    }

    public function test_a_family_asks_the_school_for_something(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $guardian = $this->guardianOf($enrollment);

        $request = app(SubmitPortalRequest::class)->submit(
            $enrollment,
            'A copy of the result slip',
            PortalRequestType::Document,
            'For a visa application.',
            $guardian,
        );

        $this->assertSame(PortalRequestStatus::Submitted, $request->status);
        $this->assertSame($enrollment->school_id, $request->school_id);
        $this->assertSame($guardian->id, $request->requested_by);
    }

    public function test_a_stranger_cannot_ask_about_a_student(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $stranger = $this->memberOf($this->workingSchool());

        $this->expectException(InvalidValueException::class);

        app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $stranger);
    }

    public function test_a_closed_requests_area_takes_no_requests(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        features()->enable(Feature::Portal, config: [PortalArea::Requests->value => false]);

        $this->expectException(InvalidValueException::class);

        app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);
    }

    public function test_the_school_answers_a_request(): void
    {
        $this->authorized_user(['answer portal request']);
        $staff = auth()->user();
        $enrollment = $this->enrollment();
        $action = app(SubmitPortalRequest::class);
        $request = $action->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);

        $action->changeStatus($request, PortalRequestStatus::InReview, $staff);
        $action->answer($request, 'The slip is ready at the office.', $staff);

        $this->assertSame(PortalRequestStatus::Answered, $request->fresh()->status);
        $this->assertSame($staff->id, $request->fresh()->answered_by);
        $this->assertNotNull($request->fresh()->answered_at);
    }

    public function test_an_answered_request_is_finished(): void
    {
        $this->authorized_user(['answer portal request']);
        $enrollment = $this->enrollment();
        $action = app(SubmitPortalRequest::class);
        $request = $action->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);
        $action->answer($request, 'The slip is ready at the office.');

        $this->expectException(InvalidValueException::class);

        $action->changeStatus($request, PortalRequestStatus::InReview);
    }

    public function test_a_family_never_answers_its_own_request(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $request = app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);

        $this->assertTrue(Gate::forUser($enrollment->user)->allows('view', $request));
        $this->assertFalse(Gate::forUser($enrollment->user)->allows('answer', $request));
    }

    public function test_another_school_never_reads_the_request(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $request = app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);

        $this->authorized_user(['read portal request', 'answer portal request'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $request));
        $this->assertFalse(Gate::forUser(auth()->user())->allows('answer', $request));
    }

    public function test_the_school_reads_a_request_with_the_permission(): void
    {
        $this->unauthorized_user();
        $enrollment = $this->enrollment();
        $request = app(SubmitPortalRequest::class)->submit($enrollment, 'A copy of the result slip', person: $enrollment->user);

        $this->authorized_user(['read portal request']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $request->fresh()));
        $this->assertSame(1, PortalRequest::inSchool()->open()->count());
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Make a guardian recorded against the student.
     */
    private function guardianOf(StudentRecord $enrollment): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($enrollment->user);

        return $guardian->fresh();
    }

    /**
     * Publish a result for one student in one subject.
     */
    private function publishedResult(StudentRecord $enrollment, Subject $subject, float $percentage, int $revision = 1): ResultSnapshot
    {
        return ResultSnapshot::create([
            'school_id'         => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'subject_id'        => $subject->id,
            'academic_year_id'  => current_academic_year_id(),
            'semester_id'       => current_semester_id(),
            'revision'          => $revision,
            'percentage'        => $percentage,
            'payload'           => ['percentage' => $percentage],
            'published_at'      => now(),
        ]);
    }
}
