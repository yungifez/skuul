<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AuditAction;
use App\Livewire\ManageAccountPassword;
use App\Models\AccountInvitation;
use App\Models\AuditEvent;
use App\Models\User;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;
use Tests\TestCase;

class AccountPasswordTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_authorized_user_can_set_a_password_for_an_invited_account(): void
    {
        $target = User::factory()->invited()->create();
        $invitation = AccountInvitation::factory()->create(['user_id' => $target->id]);

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountPassword::class, ['user' => $target])
            ->set('password', 'New-Password-123!')
            ->set('password_confirmation', 'New-Password-123!')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('isOpen', false);

        $target = $target->fresh();

        $this->assertSame(AccountStatus::Active, $target->account_status);
        $this->assertTrue(Hash::check('New-Password-123!', $target->password));
        $this->assertNull($target->password_change_required_at);
        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AccountPasswordChanged)
            ->forSubject($target)
            ->first());
    }

    public function test_setting_a_password_does_not_lift_a_suspension_or_an_archive(): void
    {
        $this->authorized_user(['manage account access']);

        foreach ([User::factory()->suspended()->create(), User::factory()->archived()->create()] as $target) {
            $status = $target->account_status;

            Livewire::test(ManageAccountPassword::class, ['user' => $target])
                ->set('password', 'New-Password-123!')
                ->set('password_confirmation', 'New-Password-123!')
                ->call('save')
                ->assertHasNoErrors();

            $this->assertSame($status, $target->fresh()->account_status);
            $this->assertTrue(Hash::check('New-Password-123!', $target->fresh()->password));
        }
    }

    public function test_an_authorized_user_can_require_a_password_change_at_next_sign_in(): void
    {
        $school = $this->workingSchool();
        $target = User::factory()->create([
            'password' => Hash::make('Temporary-Password-123!'),
        ]);

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountPassword::class, ['user' => $target])
            ->set('password', 'Temporary-Password-123!')
            ->set('password_confirmation', 'Temporary-Password-123!')
            ->set('forceReset', true)
            ->call('save')
            ->assertHasNoErrors();

        $target->refresh();
        $this->assertNotNull($target->password_change_required_at);
        $this->assertTrue($target->hasVerifiedEmail());
        $this->assertTrue($target->hasActiveAccount());
        $this->assertTrue($target->belongsToSchool($school));

        $this->flushSession();
        school_context()->set($school, remember: false);

        $this->actingAs($target)
            ->withSession([SchoolContext::SESSION_KEY => $school->id])
            ->get('/dashboard')
            ->assertRedirect(route('profile.show').'#update-password');
    }

    public function test_a_user_clears_a_forced_password_change_when_they_choose_a_new_password(): void
    {
        $target = User::factory()->create([
            'password' => Hash::make('Temporary-Password-123!'),
            'password_change_required_at' => now(),
        ]);

        Livewire::actingAs($target)
            ->test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => 'Temporary-Password-123!',
                'password' => 'New-Password-456!',
                'password_confirmation' => 'New-Password-456!',
            ])
            ->call('updatePassword');

        $target = $target->fresh();

        $this->assertTrue(Hash::check('New-Password-456!', $target->password));
        $this->assertNull($target->password_change_required_at);
    }

    public function test_a_password_must_be_confirmed(): void
    {
        $target = User::factory()->create(['password' => Hash::make('Original-Password-123!')]);
        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountPassword::class, ['user' => $target])
            ->set('isOpen', true)
            ->set('password', 'New-Password-123!')
            ->set('password_confirmation', 'Different-Password-123!')
            ->call('save')
            ->assertHasErrors('password')
            ->assertSet('isOpen', true);

        $this->assertTrue(Hash::check('Original-Password-123!', $target->fresh()->password));
    }

    public function test_an_unauthorized_user_cannot_set_another_persons_password(): void
    {
        $target = User::factory()->create([
            'password' => Hash::make('Original-Password-123!'),
        ]);

        $this->unauthorized_user();

        Livewire::test(ManageAccountPassword::class, ['user' => $target])->assertForbidden();

        $this->assertTrue(Hash::check('Original-Password-123!', $target->fresh()->password));
    }
}
