<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\User;
use App\Notifications\AccountInvitationNotification;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Administrators provision accounts. Nobody sets another person's password.
 */
class AccountProvisioningTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;
    use WithFaker;

    /**
     * Build the fields the create-admin screen posts.
     *
     * @return array<string, mixed>
     */
    private function adminFields(string $email): array
    {
        return [
            'name'        => 'Test Admin Cody',
            'email'       => $email,
            'gender'      => 'male',
            'nationality' => 'nigeria',
            'state'       => 'lagos',
            'city'        => 'lagos',
            'address'     => 'test address',
            'birthday'    => '2004/04/22',
            'phone'       => '08080808080',
        ];
    }

    public function test_creating_an_admin_provisions_an_invited_account_with_no_password()
    {
        Notification::fake();

        $email = $this->faker()->unique()->freeEmail();

        $this->authorized_user(['create admin'])
            ->post('dashboard/admins', $this->adminFields($email))
            ->assertRedirect();

        $admin = User::where('email', $email)->firstOrFail();

        $this->assertSame(AccountStatus::Invited, $admin->account_status);
        $this->assertNull($admin->password);
        $this->assertTrue($admin->hasRole('admin'));

        Notification::assertSentTo($admin, AccountInvitationNotification::class);
        $this->assertDatabaseCount('account_invitations', 1);
    }

    public function test_a_provisioned_account_cannot_sign_in_before_it_accepts_the_invitation()
    {
        Notification::fake();

        $email = $this->faker()->unique()->freeEmail();

        $this->authorized_user(['create admin'])
            ->post('dashboard/admins', $this->adminFields($email))
            ->assertRedirect();

        $this->post('/logout');

        $this->post('/login', [
            'email'    => $email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_provisioning_the_same_email_again_does_not_create_a_second_login()
    {
        Notification::fake();

        $email = $this->faker()->unique()->freeEmail();

        $this->authorized_user(['create admin'])
            ->post('dashboard/admins', $this->adminFields($email))
            ->assertRedirect();

        $this->authorized_user(['create admin'])
            ->post('dashboard/admins', $this->adminFields($email))
            ->assertRedirect();

        $this->assertSame(1, User::where('email', $email)->count());
    }
}
