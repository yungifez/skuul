<?php

namespace Tests\Feature;

use App\Actions\Finance\ReceivePayment;
use App\Enums\Role;
use App\Livewire\DashboardTrends;
use App\Livewire\ShowStudentAccount;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A family sees its own money, never another family's or the school's.
 *
 * Parents and learners hold "read fee invoice" so the portal can show their
 * own bills. That permission alone must not open the office's screens.
 */
class PortalFinancePrivacyTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_parent_cannot_open_another_learners_account(): void
    {
        $other = $this->enrollment();
        $this->signInAs($this->parent());

        $this->get(route('student-accounts.show', $other))->assertForbidden();
        Livewire::test(ShowStudentAccount::class, ['enrollment' => $other])->assertForbidden();
    }

    public function test_a_parent_cannot_open_their_own_childs_office_account(): void
    {
        $child = $this->enrollment();
        $this->signInAs($this->parent($child));

        // The portal shows the family its bills. The office screen holds
        // staff actions and the books.
        $this->get(route('student-accounts.show', $child))->assertForbidden();
    }

    public function test_a_learner_cannot_open_another_learners_account(): void
    {
        $other = $this->enrollment();
        $learner = $this->enrollment()->user;
        $learner->assignRole(Role::Student);
        $this->signInAs($learner);

        $response = $this->get(route('student-accounts.show', $other));

        $this->assertContains($response->status(), [302, 403]);
        $response->assertDontSee('Credit held');
    }

    public function test_a_receipt_does_not_open_for_another_family(): void
    {
        $child = $this->enrollment();
        $payment = app(ReceivePayment::class)->receive($child, 5_000);
        $this->signInAs($this->parent());

        $this->get(route('student-payments.receipt', $payment))->assertForbidden();
    }

    public function test_a_receipt_opens_for_the_learners_own_guardian(): void
    {
        $child = $this->enrollment();
        $payment = app(ReceivePayment::class)->receive($child, 5_000);
        $this->signInAs($this->parent($child));

        $this->get(route('student-payments.receipt', $payment))->assertOk();
    }

    public function test_the_office_still_opens_any_account_and_receipt(): void
    {
        $child = $this->enrollment();
        $payment = app(ReceivePayment::class)->receive($child, 5_000);
        $this->authorized_user(['read fee invoice']);

        $this->get(route('student-accounts.show', $child))->assertOk();
        $this->get(route('student-payments.receipt', $payment))->assertOk();
    }

    public function test_a_parent_sees_no_school_wide_figures_on_the_dashboard(): void
    {
        $this->enrollment();
        $this->signInAs($this->parent());

        Livewire::test(DashboardTrends::class)
            ->assertSet('fees', null)
            ->assertSet('owed', null)
            ->assertSet('enrolment', null);

        $this->get(route('dashboard'))->assertOk()->assertDontSee('Active students');
    }

    private function enrollment(): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
    }

    /**
     * Make a guardian with the seeded parent role, linked to the given child.
     */
    private function parent(?StudentRecord $child = null): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        school_context()->set($this->workingSchool(), remember: false);
        $guardian->assignRole(Role::Parent);
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);

        if ($child !== null) {
            $guardian->refresh()->parentRecord->students()->syncWithoutDetaching($child->user);
        }

        return $guardian->fresh();
    }

    private function signInAs(User $user): void
    {
        $this->actingAsMemberOf($this->workingSchool(), $user);
    }
}
