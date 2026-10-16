<?php

namespace Tests\Feature;

use App\Console\Commands\RefreshDemoData;
use App\Livewire\ListNoticesTable;
use App\Models\Notice;
use App\Models\User;
use App\Services\Demo\DemoDataRefresher;
use App\Traits\FeatureTestTrait;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_reset_refuses_to_run_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);
        $this->mock(DemoDataRefresher::class)->shouldNotReceive('refresh');

        $this->artisan(RefreshDemoData::class)
            ->expectsOutputToContain('Demo mode is off')
            ->assertFailed();
    }

    public function test_the_reset_runs_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $this->mock(DemoDataRefresher::class)->shouldReceive('refresh')->once();

        $this->artisan(RefreshDemoData::class)
            ->expectsOutputToContain('The demo school is reset.')
            ->assertSuccessful();
    }

    public function test_the_reset_keeps_the_migration_history_and_the_world_data(): void
    {
        $preserved = app(DemoDataRefresher::class)->preservedTables();

        $this->assertSame(['migrations', 'countries', 'states', 'cities', 'timezones', 'currencies', 'languages'], $preserved);
    }

    public function test_the_reset_is_scheduled_hourly_only_in_demo_mode(): void
    {
        $reset = collect(app(Schedule::class)->events())
            ->first(fn (Event $event): bool => str_contains((string) $event->command, 'skuul:refresh-demo'));

        $this->assertNotNull($reset);
        $this->assertSame('0 * * * *', $reset->expression);

        config(['demo.enabled' => false]);
        $this->assertFalse($reset->filtersPass($this->app));

        config(['demo.enabled' => true]);
        $this->assertTrue($reset->filtersPass($this->app));
    }

    public function test_a_visitor_cannot_delete_a_record_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        Livewire::test(ListNoticesTable::class)
            ->call('deleteNotice', $notice->id)
            ->assertDispatched('status-message', type: 'danger', message: 'Deleting is turned off in the demo. Every other change works, and the demo resets every hour.');

        $this->assertModelExists($notice);
    }

    public function test_a_visitor_can_delete_a_record_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);
        $this->authorized_user(['read notice', 'delete notice']);

        Livewire::test(ListNoticesTable::class)->call('deleteNotice', $notice->id);

        $this->assertModelMissing($notice);
    }

    public function test_a_command_can_still_delete_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $notice = Notice::factory()->create(['school_id' => $this->workingSchool()->id]);

        $notice->delete();

        $this->assertModelMissing($notice);
    }

    public function test_the_sign_in_page_offers_the_demo_accounts_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Try the demo as')
            ->assertSee('School administrator')
            ->assertSee('sarah.mitchell@staff.riversideusd.example')
            ->assertDontSee('creates your account and emails you an invitation link');
    }

    public function test_the_sign_in_page_hides_the_demo_accounts_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Try the demo as')
            ->assertDontSee('sarah.mitchell@staff.riversideusd.example')
            ->assertSee('creates your account and emails you an invitation link');
    }

    public function test_a_visitor_cannot_change_the_password_of_a_demo_account(): void
    {
        config(['demo.enabled' => true]);
        $this->actingAs($account = $this->demoAccount());

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', ['current_password' => 'password', 'password' => 'a-new-password', 'password_confirmation' => 'a-new-password'])
            ->call('updatePassword')
            ->assertDispatched('status-message', type: 'danger', message: 'The demo accounts keep their email, password, two-factor sign-in and status, so every visitor can sign in. Every other change works.');

        $this->assertTrue(Hash::check('password', $account->fresh()->password));
    }

    public function test_a_visitor_cannot_turn_on_two_factor_sign_in_for_a_demo_account(): void
    {
        config(['demo.enabled' => true]);
        $this->actingAs($account = $this->demoAccount());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        Livewire::test(TwoFactorAuthenticationForm::class)
            ->call('enableTwoFactorAuthentication')
            ->assertDispatched('status-message', type: 'danger');

        $this->assertNull($account->fresh()->two_factor_secret);
    }

    public function test_a_visitor_cannot_change_the_email_of_a_demo_account(): void
    {
        config(['demo.enabled' => true]);
        $this->actingAs($account = $this->demoAccount());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['name' => $account->name, 'email' => 'sarah.mitchell@gmail.com'])
            ->call('updateProfileInformation')
            ->assertDispatched('status-message', type: 'danger');

        $this->assertSame('sarah.mitchell@staff.riversideusd.example', $account->fresh()->email);
    }

    public function test_a_visitor_can_still_rename_a_demo_account(): void
    {
        config(['demo.enabled' => true]);
        $this->actingAs($account = $this->demoAccount());

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['name' => 'Sarah M. Mitchell', 'email' => $account->email])
            ->call('updateProfileInformation')
            ->assertNotDispatched('status-message');

        $this->assertSame('Sarah M. Mitchell', $account->fresh()->name);
    }

    public function test_an_account_outside_the_demo_list_can_change_its_password_in_demo_mode(): void
    {
        config(['demo.enabled' => true]);
        $this->actingAs($account = User::factory()->create(['password' => Hash::make('password')]));

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', ['current_password' => 'password', 'password' => 'a-new-password', 'password_confirmation' => 'a-new-password'])
            ->call('updatePassword');

        $this->assertTrue(Hash::check('a-new-password', $account->fresh()->password));
    }

    public function test_the_demo_account_lock_is_off_outside_demo_mode(): void
    {
        config(['demo.enabled' => false]);
        $this->actingAs($account = $this->demoAccount());

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', ['current_password' => 'password', 'password' => 'a-new-password', 'password_confirmation' => 'a-new-password'])
            ->call('updatePassword');

        $this->assertTrue(Hash::check('a-new-password', $account->fresh()->password));
    }

    /**
     * A seeded demo account, signed in with the demo password.
     */
    private function demoAccount(): User
    {
        return User::factory()->create([
            'name' => 'Sarah Mitchell',
            'email' => 'sarah.mitchell@staff.riversideusd.example',
            'password' => Hash::make('password'),
        ]);
    }
}
