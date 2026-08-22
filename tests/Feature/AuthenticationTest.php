<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('data-ui="april"', false);
        $response->assertSee('data-slot="input"', false);
        $response->assertSee('data-slot="button"', false);
    }

    public function test_login_screen_has_accessible_credential_controls(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Email address')
            ->assertSee('name="email"', false)
            ->assertSee('autocomplete="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee('Remember me')
            ->assertSee('Log in');

        $html = (string) $response->getContent();

        $this->assertSame(1, substr_count($html, 'id="email"'));
        $this->assertSame(1, substr_count($html, 'id="password"'));
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();
        // since factory produces random password, it had to be changed
        $user->password = Hash::make('password');
        $user->save();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(config('fortify.home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
