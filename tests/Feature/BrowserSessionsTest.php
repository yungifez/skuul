<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Livewire\Livewire;
use Tests\TestCase;

class BrowserSessionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The form only ends other sessions when they are stored in the database.
        config(['session.driver' => 'database']);

        // Livewire test requests skip middleware, so no session is started for
        // them. Attach the session store the same way the middleware would.
        Event::listen(RouteMatched::class, function (RouteMatched $event): void {
            $event->request->setLaravelSession(app('session.store'));
        });

        $this->startSession();
    }

    public function test_other_browser_sessions_can_be_logged_out(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);

        $this->insertSessionRecord('another-device', $user->id);
        $this->insertSessionRecord(app('session.store')->getId(), $user->id);
        $otherUser = User::factory()->create();
        $this->insertSessionRecord('another-person', $otherUser->id);

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->assertSeeHtml('wire:click="confirmLogout"')
            ->call('confirmLogout')
            ->assertSet('confirmingLogout', true)
            ->assertDispatched('confirming-logout-other-browser-sessions')
            ->set('password', 'password')
            ->call('logoutOtherBrowserSessions')
            ->assertSet('confirmingLogout', false)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('sessions', ['id' => 'another-device']);
        $this->assertDatabaseHas('sessions', ['id' => app('session.store')->getId()]);
        $this->assertDatabaseHas('sessions', ['id' => 'another-person', 'user_id' => $otherUser->id]);
    }

    public function test_the_current_password_is_required_to_log_out_other_sessions(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);

        $this->insertSessionRecord('another-device', $user->id);

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->set('password', 'not-the-password')
            ->call('logoutOtherBrowserSessions')
            ->assertHasErrors(['password']);

        $this->assertDatabaseHas('sessions', ['id' => 'another-device']);
    }

    public function test_the_confirmation_dialog_stays_hidden_until_alpine_starts(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->assertSeeHtml('x-bind="overlay" x-cloak')
            ->assertSeeHtml('x-effect="open = $wire.confirmingLogout"')
            ->assertDontSeeHtml('x-effect="show = $wire.confirmingLogout"');
    }

    public function test_other_session_controls_require_database_sessions(): void
    {
        config(['session.driver' => 'array']);
        $this->actingAs(User::factory()->create());

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->assertDontSeeHtml('wire:click="confirmLogout"')
            ->assertSee('Managing other browser sessions is unavailable on this site.');
    }

    /**
     * Store a session record for a second device of the same person.
     */
    private function insertSessionRecord(string $id, int $userId): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $userId,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
            'payload' => '',
            'last_activity' => now()->getTimestamp(),
        ]);
    }
}
