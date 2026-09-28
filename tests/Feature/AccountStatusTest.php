<?php

namespace Tests\Feature;

use App\Actions\Identity\ChangeAccountStatus;
use App\Actions\School\EndSchoolMembership;
use App\Enums\AccountStatus;
use App\Events\AccountStatusChanged;
use App\Exceptions\InvalidValueException;
use App\Livewire\ManageAccountAccess;
use App\Models\AccountInvitation;
use App\Models\School;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AccountStatusTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_authorized_user_can_suspend_an_account()
    {
        $target = User::factory()->create();

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('suspend')
            ->assertHasNoErrors();

        $this->assertSame(AccountStatus::Suspended, $target->fresh()->account_status);
    }

    public function test_the_last_role_manager_of_a_campus_is_not_blocked(): void
    {
        $campus = School::factory()->create();
        school_context()->set($campus, remember: false);
        $first = $this->memberOf($campus);
        $first->givePermissionTo('manage role');
        $second = $this->memberOf($campus);
        $second->givePermissionTo('manage role');
        $changeAccountStatus = app(ChangeAccountStatus::class);

        $changeAccountStatus->suspend($first);

        foreach (['suspend', 'archive'] as $block) {
            try {
                $changeAccountStatus->{$block}($second);
                $this->fail('The campus was left with nobody who can manage roles.');
            } catch (InvalidValueException $exception) {
                $this->assertStringContainsString('Nobody at this campus could manage roles', $exception->getMessage());
            }
        }

        $this->assertSame(AccountStatus::Active, $second->fresh()->account_status);

        try {
            app(EndSchoolMembership::class)->end($second, $campus);
            $this->fail('The only role manager who can sign in left the campus.');
        } catch (InvalidValueException) {
            $this->assertTrue($second->refresh()->belongsToSchool($campus));
        }
    }

    public function test_blocking_the_last_role_manager_is_refused_on_the_screen(): void
    {
        $campus = School::factory()->create();
        $this->authorized_user(['manage account access'], $campus);
        $manager = $this->memberOf($campus);
        $manager->givePermissionTo('manage role');

        Livewire::test(ManageAccountAccess::class, ['user' => $manager])
            ->call('suspend')
            ->assertDispatched('status-message', type: 'danger');

        $this->assertSame(AccountStatus::Active, $manager->fresh()->account_status);
    }

    public function test_authorized_user_can_reinstate_a_suspended_account()
    {
        $target = User::factory()->suspended()->create();

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('reinstate')
            ->assertHasNoErrors();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_reinstating_an_account_with_no_password_returns_it_to_invited()
    {
        $target = User::factory()->invited()->create();
        $target->forceFill(['account_status' => AccountStatus::Suspended])->save();

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('reinstate')
            ->assertHasNoErrors();

        $this->assertSame(AccountStatus::Invited, $target->fresh()->account_status);
    }

    public function test_unauthorized_user_cannot_change_an_account_status()
    {
        $target = User::factory()->create();

        $this->unauthorized_user();

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('suspend')
            ->assertForbidden();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_an_administrator_cannot_change_an_account_in_another_school()
    {
        $otherSchool = School::factory()->create();
        $target = $this->memberOf($otherSchool, User::factory()->create());
        $target->schoolMemberships()->where('school_id', '!=', $otherSchool->id)->delete();

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('suspend')
            ->assertForbidden();

        $this->assertSame(AccountStatus::Active, $target->fresh()->account_status);
    }

    public function test_a_user_cannot_change_their_own_account_status()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage account access');

        $this->actingAs($user);

        Livewire::test(ManageAccountAccess::class, ['user' => $user])
            ->call('suspend')
            ->assertForbidden();

        $this->assertSame(AccountStatus::Active, $user->fresh()->account_status);
    }

    public function test_a_suspended_account_is_offered_only_reinstatement()
    {
        $target = User::factory()->suspended()->create();
        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->assertSee('Suspended')
            ->assertSee('Reinstate account')
            ->assertDontSee('Suspend account')
            ->assertDontSee('Archive account');
    }

    public function test_the_account_menu_is_hidden_without_access()
    {
        $target = User::factory()->create();
        $this->unauthorized_user();

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->assertSee('Active')
            ->assertDontSee('Suspend account');
    }

    public function test_an_administrator_archives_an_account()
    {
        $target = User::factory()->create();
        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('archive')
            ->assertDispatched('status-message', type: 'success', message: "Set {$target->name}'s account to Archived.");

        $this->assertSame(AccountStatus::Archived, $target->fresh()->account_status);
    }

    public function test_suspending_an_account_revokes_its_pending_invitations()
    {
        $target = User::factory()->invited()->create();
        AccountInvitation::factory()->create(['user_id' => $target->id]);

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('suspend')
            ->assertHasNoErrors();

        $this->assertNotNull($target->accountInvitations()->first()->revoked_at);
    }

    public function test_changing_a_status_raises_the_audit_event()
    {
        Event::fake([AccountStatusChanged::class]);

        $target = User::factory()->create();

        $this->authorized_user(['manage account access']);

        Livewire::test(ManageAccountAccess::class, ['user' => $target])
            ->call('suspend')
            ->assertHasNoErrors();

        Event::assertDispatched(AccountStatusChanged::class, function (AccountStatusChanged $event) use ($target): bool {
            return $event->user->is($target)
                && $event->from === AccountStatus::Active
                && $event->to === AccountStatus::Suspended
                && $event->reason === null;
        });
    }

    public function test_a_platform_administrator_account_cannot_be_suspended()
    {
        $superAdmin = User::factory()->platformAdmin()->create();

        $this->expectException(\RuntimeException::class);

        app(ChangeAccountStatus::class)->suspend($superAdmin);
    }

    public function test_a_suspended_account_cannot_use_the_dashboard()
    {
        $user = User::factory()->suspended()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }

    public function test_an_archived_account_cannot_use_the_dashboard()
    {
        $user = User::factory()->archived()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }

    public function test_an_active_account_can_use_the_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_an_invited_account_cannot_sign_in()
    {
        $user = User::factory()->invited()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_a_suspended_account_with_a_valid_password_is_blocked_from_the_dashboard()
    {
        $user = User::factory()->suspended()->create([
            'password' => Hash::make('Str0ng-Passw0rd!'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Str0ng-Passw0rd!',
        ]);

        $this->get('/dashboard')->assertForbidden();
        $this->assertGuest();
    }
}
