<?php

namespace Tests\Feature;

use App\Actions\Sharing\FulfilDataSharingRequest;
use App\Actions\Sharing\RequestDataSharing;
use App\Enums\DataCategory;
use App\Enums\DataSharingStatus;
use App\Livewire\CreateDataSharingRequestForm;
use App\Livewire\ShowDataSharingRequest;
use App\Models\DataSharingRequest;
use App\Models\School;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\TransferPackage;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        $this->get(route('data-sharing-requests.create'))->assertOk()->assertSee($holder->name);

        $form = Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $holder->id)
            ->set('admissionNumber', " {$enrollment->admission_number} ")
            ->set('purpose', 'The learner transferred to us in September.')
            ->set('categories', [DataCategory::Enrollment->value, DataCategory::AcademicResults->value])
            ->call('save')
            ->assertHasNoErrors();

        $request = DataSharingRequest::sole();

        $form->assertRedirect(route('data-sharing-requests.show', $request));
        $this->assertSame($enrollment->id, $request->student_record_id);
        $this->assertSame($holder->id, $request->holding_school_id);
        $this->assertSame(DataSharingStatus::Requested, $request->status);
    }

    public function test_a_wrong_admission_number_says_nothing_about_the_other_school(): void
    {
        $holder = School::factory()->create();
        StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $holder->id)
            ->set('admissionNumber', 'NOT-A-REAL-NUMBER')
            ->set('purpose', 'Fishing.')
            ->set('categories', [DataCategory::Enrollment->value])
            ->call('save')
            ->assertHasErrors('admissionNumber');

        $this->assertSame(0, DataSharingRequest::count());
    }

    public function test_guessing_admission_numbers_stops_after_ten_misses(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        $form = Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $holder->id)
            ->set('purpose', 'Fishing.')
            ->set('categories', [DataCategory::Enrollment->value]);

        foreach (range(1, 10) as $guess) {
            $form->set('admissionNumber', "GUESS-{$guess}")->call('save');
        }

        $form->set('admissionNumber', $enrollment->admission_number)
            ->call('save')
            ->assertHasErrors('admissionNumber')
            ->assertSee('Too many admission numbers were not found');

        $this->assertSame(0, DataSharingRequest::count());
    }

    public function test_a_school_cannot_ask_itself(): void
    {
        $this->authorized_user(['request data sharing']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);

        Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $this->workingSchool()->id)
            ->set('admissionNumber', $enrollment->admission_number)
            ->set('purpose', 'Asking myself.')
            ->set('categories', [DataCategory::Enrollment->value])
            ->call('save')
            ->assertHasErrors('holdingSchoolId');

        $this->assertSame(0, DataSharingRequest::count());
    }

    public function test_a_request_must_name_what_it_asks_for(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $holder->id)
            ->set('admissionNumber', $enrollment->admission_number)
            ->set('purpose', 'Everything please.')
            ->call('save')
            ->assertHasErrors('categories');
    }

    public function test_asking_twice_for_the_same_learner_sends_one_request(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);

        $ask = fn () => Livewire::test(CreateDataSharingRequestForm::class)
            ->set('holdingSchoolId', (string) $holder->id)
            ->set('admissionNumber', $enrollment->admission_number)
            ->set('purpose', 'The learner transferred to us.')
            ->set('categories', [DataCategory::Enrollment->value])
            ->call('save');

        $ask()->assertHasNoErrors();
        $ask()->assertHasErrors('holdingSchoolId');

        $this->assertSame(1, DataSharingRequest::count());
    }

    public function test_the_ask_form_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('data-sharing-requests.create'))->assertForbidden();
        Livewire::test(CreateDataSharingRequestForm::class)->assertForbidden();
    }

    public function test_the_holding_school_answers_the_request(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing']);

        $this->get(route('data-sharing-requests.show', $request))
            ->assertOk()
            ->assertSeeLivewire(ShowDataSharingRequest::class);

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertSee('Approve')
            ->set('note', 'The guardian agreed.')
            ->call('decide', DataSharingStatus::Approved->value)
            ->assertDispatched('status-message', message: 'Request Approved.')
            ->assertSee('The guardian agreed.');

        $this->assertSame(DataSharingStatus::Approved, $request->fresh()->status);
        $this->assertSame('The guardian agreed.', $request->fresh()->decision_note);
    }

    public function test_the_holding_school_sees_who_it_is_asked_about(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing']);

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertSeeInOrder([$request->studentRecord->user->name, $request->studentRecord->admission_number]);
    }

    public function test_the_asking_school_sees_only_the_admission_number(): void
    {
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);
        $this->authorized_user(['request data sharing']);
        $request = app(RequestDataSharing::class)->request(
            $enrollment,
            $this->workingSchool(),
            'The learner transferred to us.',
            [DataCategory::Enrollment],
        );

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertSee($enrollment->admission_number)
            ->assertDontSee($enrollment->user->name);
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

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertDontSee('Approve')
            ->call('decide', DataSharingStatus::Approved->value)
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

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->call('fulfil')
            ->assertDispatched('status-message', type: 'success')
            ->assertDontSee('Nothing has been handed over');

        $this->assertSame(1, TransferPackage::count());
        $this->assertSame(DataSharingStatus::Fulfilled, $request->fresh()->status);
    }

    public function test_a_request_that_was_not_approved_cannot_be_handed_over(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing']);

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertDontSee('Hand the records over')
            ->call('fulfil')
            ->assertDispatched('status-message', type: 'danger');

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

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->assertSee('Take the records in')
            ->call('receive')
            ->assertDispatched('status-message', message: 'Records taken in.')
            ->assertDontSee('Take the records in');

        $this->assertTrue($package->fresh()->wasReceived());
    }

    public function test_the_asking_school_reads_the_records_once_it_took_them_in(): void
    {
        $asking = $this->workingSchool();
        $holder = School::factory()->create();
        $learner = User::factory()->create(['name' => 'Moved Learner']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id, 'user_id' => $learner->id, 'admission_number' => 'OLD-4471']);
        StudentHealthRecord::create(['school_id' => $holder->id, 'student_record_id' => $enrollment->id, 'allergies' => 'Peanuts']);

        $this->authorized_user(['request data sharing'], $asking);
        $request = app(RequestDataSharing::class)->request(
            $enrollment,
            $asking,
            'The learner transferred to us.',
            [DataCategory::Identity, DataCategory::Enrollment, DataCategory::Health, DataCategory::Discipline],
        );
        $this->authorized_user(['approve data sharing', 'fulfil data sharing'], $holder);
        app(RequestDataSharing::class)->approve($request, auth()->user());
        app(FulfilDataSharingRequest::class)->fulfil($request, auth()->user());

        // The school that sent the copy holds the originals, not the copy.
        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->assertDontSee('Peanuts');

        // The permission can still be taken back until the records are taken in.
        $this->authorized_user(['request data sharing'], $asking);
        $screen = Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->assertDontSee('Peanuts')
            ->assertDontSee('Admission date')
            ->call('receive');

        $screen->assertSeeInOrder(['Identity', 'Moved Learner', 'Enrollment', 'Admission date', 'Health', 'Peanuts', 'Discipline', 'Nothing on record'])
            ->assertSeeHtml('<dd class="font-medium break-words">—</dd>')
            ->assertDontSee('Student record id');
    }

    public function test_shared_figures_read_in_the_order_they_were_built(): void
    {
        $asking = $this->workingSchool();
        $holder = School::factory()->create();
        $enrollment = StudentRecord::factory()->create(['school_id' => $holder->id]);

        $this->authorized_user(['request data sharing'], $asking);
        $request = app(RequestDataSharing::class)->request($enrollment, $asking, 'The learner transferred to us.', [DataCategory::Attendance]);
        $this->authorized_user(['approve data sharing', 'fulfil data sharing'], $holder);
        app(RequestDataSharing::class)->approve($request, auth()->user());
        $package = app(FulfilDataSharingRequest::class)->fulfil($request, auth()->user());

        $this->authorized_user(['request data sharing'], $asking);
        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->call('receive')
            ->assertSeeInOrder(['Present', 'Absent', 'Late', 'Excused', 'Recorded', 'Rate'])
            ->assertDontSee('__keys')
            ->assertDontSee('Keys');

        $this->assertSame(0, $package->fresh()->payload['attendance']['present']);
    }

    public function test_the_holding_school_takes_the_permission_back(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing']);

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request])
            ->assertSee('Take permission back')
            ->call('decide', DataSharingStatus::Revoked->value)
            ->assertDontSee('Approve');

        $this->assertSame(DataSharingStatus::Revoked, $request->fresh()->status);
    }

    public function test_handing_over_is_not_a_status_anyone_can_pick(): void
    {
        $request = $this->requestForThisSchool();
        $this->authorized_user(['request data sharing', 'approve data sharing', 'fulfil data sharing']);
        app(RequestDataSharing::class)->approve($request, auth()->user());

        Livewire::test(ShowDataSharingRequest::class, ['sharingRequest' => $request->fresh()])
            ->call('decide', DataSharingStatus::Fulfilled->value)
            ->assertStatus(422);

        $this->assertSame(0, TransferPackage::count());
        $this->assertSame(DataSharingStatus::Approved, $request->fresh()->status);
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
