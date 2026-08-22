<?php

namespace Tests\Feature;

use App\Actions\Sharing\FulfilDataSharingRequest;
use App\Actions\Sharing\RequestDataSharing;
use App\Actions\Wellbeing\RecordHealthInformation;
use App\Enums\AuditAction;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AuditEvent;
use App\Models\CourseOffering;
use App\Models\DataSharingRequest;
use App\Models\ResultSnapshot;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\Subject;
use App\Models\TransferPackage;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Tests\TestCase;

/**
 * One school reads another school's records only through an approved request,
 * and only the categories that were approved.
 */
class DataSharingTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    private ?CourseOffering $courseOffering = null;

    public function test_a_school_asks_another_for_named_categories(): void
    {
        $this->authorized_user(['request data sharing']);
        $enrollment = $this->enrollment();
        $asking = School::factory()->create();

        $request = app(RequestDataSharing::class)->request(
            $enrollment,
            $asking,
            'The student applied to us.',
            [DataCategory::Identity, DataCategory::Enrollment],
            now()->addMonth(),
        );

        $this->assertSame(DataSharingStatus::Requested, $request->status);
        $this->assertSame($enrollment->school_id, $request->holding_school_id);
        $this->assertSame($asking->id, $request->requesting_school_id);
        $this->assertSame(['identity', 'enrollment'], $request->categories);
    }

    public function test_a_school_does_not_ask_itself(): void
    {
        $this->authorized_user(['request data sharing']);
        $enrollment = $this->enrollment();

        $this->expectException(InvalidValueException::class);

        app(RequestDataSharing::class)->request($enrollment, $this->workingSchool(), 'No reason', [DataCategory::Identity]);
    }

    public function test_a_request_must_name_what_it_asks_for(): void
    {
        $this->authorized_user(['request data sharing']);

        $this->expectException(InvalidValueException::class);

        app(RequestDataSharing::class)->request($this->enrollment(), School::factory()->create(), 'No reason', []);
    }

    public function test_nothing_is_handed_over_before_approval(): void
    {
        $this->authorized_user(['request data sharing']);
        $request = $this->request([DataCategory::Identity]);

        $this->expectException(InvalidValueException::class);

        app(FulfilDataSharingRequest::class)->fulfil($request);
    }

    public function test_a_declined_request_cannot_be_approved_later(): void
    {
        $this->authorized_user(['approve data sharing']);
        $action = app(RequestDataSharing::class);
        $request = $this->request([DataCategory::Identity]);
        $action->decline($request, note: 'The family did not agree');

        $this->expectException(InvalidValueException::class);

        $action->approve($request);
    }

    public function test_an_approved_request_hands_over_only_what_it_named(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $enrollment = $this->enrollment();
        app(RecordHealthInformation::class)->record($enrollment, ['allergies' => 'Peanuts']);
        $this->publishedResult($enrollment, 78);
        $request = $this->request([DataCategory::Identity, DataCategory::AcademicResults], $enrollment);

        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);

        $this->assertArrayHasKey('identity', $package->payload);
        $this->assertArrayHasKey('academic_results', $package->payload);
        $this->assertArrayNotHasKey('health', $package->payload);
        $this->assertEqualsWithDelta(78.0, $package->payload['academic_results'][0]['percentage'], 0.001);
        $this->assertSame($enrollment->school_id, $package->payload['source_school_id']);
        $this->assertSame(DataSharingStatus::Fulfilled, $request->fresh()->status);
    }

    public function test_a_restricted_category_travels_only_when_it_was_approved(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $enrollment = $this->enrollment();
        app(RecordHealthInformation::class)->record($enrollment, ['allergies' => 'Peanuts']);
        $request = $this->request([DataCategory::Health], $enrollment);

        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);

        $this->assertSame('Peanuts', $package->payload['health']['allergies']);
        $this->assertTrue(DataCategory::Health->isRestricted());
        $this->assertFalse(DataCategory::Identity->isRestricted());
    }

    public function test_a_permission_that_ran_out_hands_over_nothing(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $request = $this->request([DataCategory::Identity], expiresOn: now()->addDay());
        app(RequestDataSharing::class)->approve($request);
        $request->forceFill(['expires_on' => now()->subDay()])->save();

        $this->expectException(InvalidValueException::class);

        app(FulfilDataSharingRequest::class)->fulfil($request->fresh());
    }

    public function test_a_package_cannot_be_changed_after_it_is_built(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $request = $this->request([DataCategory::Identity]);
        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);

        $this->expectException(RuntimeException::class);

        $package->update(['payload' => ['identity' => ['name' => 'Somebody else']]]);
    }

    public function test_the_school_that_asked_takes_the_package_in(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $destination = School::factory()->create();
        $request = $this->request([DataCategory::Identity], asking: $destination);
        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);

        $newEnrollment = StudentRecord::factory()->create(['school_id' => $destination->id]);
        app(FulfilDataSharingRequest::class)->receive($package, $newEnrollment);

        $this->assertTrue($package->fresh()->wasReceived());
        $this->assertSame($newEnrollment->id, $package->fresh()->received_student_record_id);
    }

    public function test_a_package_is_taken_in_once(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $request = $this->request([DataCategory::Identity]);
        app(RequestDataSharing::class)->approve($request);
        $action = app(FulfilDataSharingRequest::class);
        $package = $action->fulfil($request);
        $action->receive($package);

        $this->expectException(InvalidValueException::class);

        $action->receive($package->fresh());
    }

    public function test_an_enrollment_from_a_third_school_cannot_receive_it(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $request = $this->request([DataCategory::Identity]);
        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);
        $stranger = StudentRecord::factory()->create(['school_id' => School::factory()->create()->id]);

        $this->expectException(InvalidValueException::class);

        app(FulfilDataSharingRequest::class)->receive($package, $stranger);
    }

    public function test_only_the_holding_school_decides(): void
    {
        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing']);
        $asking = School::factory()->create();
        $request = $this->request([DataCategory::Identity], asking: $asking);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('decide', $request));

        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing'], $asking);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $request));
        $this->assertFalse(Gate::forUser(auth()->user())->allows('decide', $request));
        $this->assertFalse(Gate::forUser(auth()->user())->allows('fulfil', $request));
    }

    public function test_a_third_school_reads_nothing(): void
    {
        $this->authorized_user(['request data sharing']);
        $request = $this->request([DataCategory::Identity]);

        $this->authorized_user(['request data sharing', 'approve data sharing'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $request));
    }

    public function test_the_holding_school_sees_what_it_must_answer(): void
    {
        $this->authorized_user(['request data sharing']);
        $this->request([DataCategory::Identity]);

        $this->assertSame(1, DataSharingRequest::awaiting($this->workingSchool())->count());
    }

    public function test_the_permission_can_be_taken_back(): void
    {
        $this->authorized_user(['approve data sharing']);
        $action = app(RequestDataSharing::class);
        $request = $this->request([DataCategory::Identity]);
        $action->approve($request);
        $action->revoke($request, note: 'The family objected');

        $this->assertSame(DataSharingStatus::Revoked, $request->fresh()->status);
        $this->assertFalse($request->fresh()->isUsable());
    }

    public function test_asking_and_handing_over_are_written_to_the_audit_log(): void
    {
        $this->authorized_user(['approve data sharing', 'fulfil data sharing']);
        $request = $this->request([DataCategory::Identity]);
        app(RequestDataSharing::class)->approve($request);
        $package = app(FulfilDataSharingRequest::class)->fulfil($request);

        $this->assertNotNull(AuditEvent::ofAction(AuditAction::DataSharingRequested)->forSubject($request)->first());
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::TransferPackageBuilt)->forSubject($package)->first());
        $this->assertSame(0, TransferPackage::query()->whereNull('payload')->count());
    }

    /**
     * Make a request from another school for one enrollment here.
     *
     * @param  array<int, DataCategory>  $categories
     */
    private function request(
        array $categories,
        ?StudentRecord $enrollment = null,
        ?School $asking = null,
        mixed $expiresOn = null,
    ): DataSharingRequest {
        return app(RequestDataSharing::class)->request(
            $enrollment ?? $this->enrollment(),
            $asking ?? School::factory()->create(),
            'The student applied to us.',
            $categories,
            $expiresOn,
        );
    }

    /**
     * Make an enrollment in the working school.
     */
    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Publish a result for the student.
     */
    private function publishedResult(StudentRecord $enrollment, float $percentage): ResultSnapshot
    {
        $this->courseOffering ??= CourseOffering::factory()->create([
            'school_id' => $enrollment->school_id,
            'subject_id' => Subject::factory()->create(['school_id' => $enrollment->school_id])->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_period_id' => current_academic_period_id(),
        ]);

        return ResultSnapshot::create([
            'school_id' => $enrollment->school_id,
            'student_record_id' => $enrollment->id,
            'course_offering_id' => $this->courseOffering->id,
            'revision' => 1,
            'percentage' => $percentage,
            'payload' => ['percentage' => $percentage],
            'published_at' => now(),
        ]);
    }
}
