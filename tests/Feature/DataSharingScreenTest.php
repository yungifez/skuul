<?php

namespace Tests\Feature;

use App\Actions\Sharing\FulfilDataSharingRequest;
use App\Actions\Sharing\RequestDataSharing;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Models\DataSharingRequest;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\TransferPackage;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Asking, approving, and handing over are three decisions, and the school
 * that asks still has to take the records in.
 */
class DataSharingScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_both_lists_start_empty(): void
    {
        $this->authorized_user(['request data sharing', 'approve data sharing']);

        $this->get(route('data-sharing-requests.index'))
            ->assertOk()
            ->assertSee('Nobody has asked this school for records')
            ->assertSee('This school has asked for nothing');
    }

    public function test_a_school_asks_another_by_admission_number(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        $response = $this->post(route('data-sharing-requests.store'), [
            'holding_school_id' => $holder->id,
            'admission_number' => $enrollment->admission_number,
            'purpose' => 'The learner transferred to us in September.',
            'categories' => [DataCategory::Enrollment->value, DataCategory::AcademicResults->value],
        ]);

        $request = DataSharingRequest::sole();

        $response->assertRedirect(route('data-sharing-requests.show', $request));

        $this->assertSame($enrollment->id, $request->student_record_id);
        $this->assertSame($holder->id, $request->holding_school_id);
        $this->assertSame(DataSharingStatus::Requested, $request->status);
    }

    public function test_a_wrong_admission_number_says_nothing_about_the_other_school(): void
    {
        $holder = School::factory()->create();
        StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        $this->post(route('data-sharing-requests.store'), [
            'holding_school_id' => $holder->id,
            'admission_number' => 'NOT-A-REAL-NUMBER',
            'purpose' => 'Fishing.',
            'categories' => [DataCategory::Enrollment->value],
        ])->assertSessionHasErrors('admission_number');

        $this->assertSame(0, DataSharingRequest::count());
    }

    public function test_a_school_cannot_ask_itself(): void
    {
        $this->authorized_user(['request data sharing']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);

        $this->post(route('data-sharing-requests.store'), [
            'holding_school_id' => $this->workingSchool()->id,
            'admission_number' => $enrollment->admission_number,
            'purpose' => 'Asking myself.',
            'categories' => [DataCategory::Enrollment->value],
        ])->assertSessionHasErrors('data_sharing');

        $this->assertSame(0, DataSharingRequest::count());
    }

    public function test_a_request_must_name_what_it_asks_for(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        $this->post(route('data-sharing-requests.store'), [
            'holding_school_id' => $holder->id,
            'admission_number' => $enrollment->admission_number,
            'purpose' => 'Everything please.',
        ])->assertSessionHasErrors('categories');
    }

    public function test_the_holding_school_answers_the_request(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing']);

        $this->from(route('data-sharing-requests.show', $request))
            ->put(route('data-sharing-requests.status.update', $request), [
                'status' => DataSharingStatus::Approved->value,
                'note' => 'The guardian agreed.',
            ])
            ->assertRedirect(route('data-sharing-requests.show', $request));

        $this->assertSame(DataSharingStatus::Approved, $request->fresh()->status);
    }

    public function test_the_asking_school_never_answers_its_own_request(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing', 'approve data sharing']);
        $request = app(RequestDataSharing::class)->request(
            $enrollment,
            $this->workingSchool(),
            'The learner transferred to us.',
            [DataCategory::Enrollment],
        );

        $this->from(route('data-sharing-requests.show', $request))
            ->put(route('data-sharing-requests.status.update', $request), [
                'status' => DataSharingStatus::Approved->value,
            ])
            ->assertForbidden();

        $this->assertSame(DataSharingStatus::Requested, $request->fresh()->status);
    }

    public function test_approving_does_not_hand_the_records_over(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing']);
        app(RequestDataSharing::class)->approve($request, auth()->user());

        $this->assertSame(0, TransferPackage::count());

        $this->get(route('data-sharing-requests.show', $request))
            ->assertOk()
            ->assertSee('Nothing has been handed over');

        $this->from(route('data-sharing-requests.show', $request))
            ->post(route('data-sharing-requests.fulfil', $request))
            ->assertRedirect(route('data-sharing-requests.show', $request));

        $this->assertSame(1, TransferPackage::count());
        $this->assertSame(DataSharingStatus::Fulfilled, $request->fresh()->status);
    }

    public function test_a_request_that_was_not_approved_cannot_be_handed_over(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing']);

        $this->from(route('data-sharing-requests.show', $request))
            ->post(route('data-sharing-requests.fulfil', $request))
            ->assertSessionHasErrors('fulfil');

        $this->assertSame(0, TransferPackage::count());
    }

    public function test_the_asking_school_takes_the_records_in(): void
    {
        $asking = $this->workingSchool();
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);

        $this->authorized_user(['request data sharing'], $asking);
        $request = app(RequestDataSharing::class)->request(
            $enrollment,
            $asking,
            'The learner transferred to us.',
            [DataCategory::Enrollment],
        );

        // The holding school agrees and builds the copy.
        $this->authorized_user(['approve data sharing', 'fulfil data sharing'], $holder);
        app(RequestDataSharing::class)->approve($request, auth()->user());
        $package = app(FulfilDataSharingRequest::class)->fulfil($request, auth()->user());

        $this->assertFalse($package->wasReceived());

        // Back at the school that asked, somebody takes it in.
        $this->authorized_user(['request data sharing'], $asking);

        $this->from(route('data-sharing-requests.show', $request))
            ->post(route('data-sharing-requests.packages.receive', [$request, $package]))
            ->assertRedirect(route('data-sharing-requests.show', $request));

        $this->assertTrue($package->fresh()->wasReceived());
    }

    public function test_a_school_that_is_neither_side_reads_nothing(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing'], School::factory()->create());

        $this->get(route('data-sharing-requests.show', $request))->assertForbidden();
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('data-sharing-requests.index'))->assertForbidden();
    }

    /**
     * Have another school ask this one for a learner's records.
     */
    private function requestForThisSchool(): DataSharingRequest
    {
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);

        return app(RequestDataSharing::class)->request(
            $enrollment,
            School::factory()->create(),
            'The learner applied to us.',
            [DataCategory::Enrollment, DataCategory::AcademicResults],
        );
    }
}
