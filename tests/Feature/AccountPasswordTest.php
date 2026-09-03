<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AuditAction;
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

        $actor = $this->authorized_user(['manage account access']);

        $actor->post(route('users.password.update', $target), [
            'password' => 'New-Password-123!',
            'password_confirmation' => 'New-Password-123!',
        ])->assertRedirect();

        $target = $target->fresh();

        $this->assertSame(AccountStatus::Active, $target->account_status);
        $this->assertTrue(Hash::check('New-Password-123!', $target->password));
        $this->assertNull($target->password_change_required_at);
        $this->assertNotNull($invitation->fresh()->revoked_at);
        $this->assertNotNull(AuditEvent::ofAction(AuditAction::AccountPasswordChanged)
            ->forSubject($target)
            ->first());
    }

    public function test_an_authorized_user_can_require_a_password_change_at_next_sign_in(): void
    {
        $school = $this->workingSchool();
        $target = User::factory()->create([
            'password' => Hash::make('Temporary-Password-123!'),
        ]);

        $this->authorized_user(['manage account access'])
            ->post(route('users.password.update', $target), [
                'password' => 'Temporary-Password-123!',
                'password_confirmation' => 'Temporary-Password-123!',
                'force_reset' => '1',
            ])->assertRedirect();

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

    public function test_an_unauthorized_user_cannot_set_another_persons_password(): void
    {
        $target = User::factory()->create([
            'password' => Hash::make('Original-Password-123!'),
        ]);

        $this->unauthorized_user()
            ->post(route('users.password.update', $target), [
                'password' => 'New-Password-123!',
                'password_confirmation' => 'New-Password-123!',
            ])->assertForbidden();

        $this->assertTrue(Hash::check('Original-Password-123!', $target->fresh()->password));
    }
}
