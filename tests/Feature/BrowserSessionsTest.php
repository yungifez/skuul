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

    public function test_other_browser_sessions_can_be_logged_out()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);

        $this->insertSessionRecord('another-device', $user->id);

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->set('password', 'password')
            ->call('logoutOtherBrowserSessions')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('sessions', ['id' => 'another-device']);
    }

    public function test_the_current_password_is_required_to_log_out_other_sessions()
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

    public function test_the_confirmation_dialog_stays_hidden_until_alpine_starts()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $this->actingAs($user);

        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->assertSeeHtml('x-bind="overlay" x-cloak');
    }

    /**
     * Store a session record for a second device of the same person.
     */
    private function insertSessionRecord(string $id, int $userId): void
    {
        DB::table('sessions')->insert([
            'id'            => $id,
            'user_id'       => $userId,
            'ip_address'    => '127.0.0.1',
            'user_agent'    => 'phpunit',
            'payload'       => '',
            'last_activity' => now()->getTimestamp(),
        ]);
    }
}
