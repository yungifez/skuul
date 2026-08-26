<?php

namespace Tests\Feature;

use App\Livewire\NationalityAndStateInputFields;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_current_profile_information_is_available()
    {
        $this->actingAs($user = User::factory()->create());

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function test_the_profile_screen_uses_april_ui_sections(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('profile.show'))
            ->assertOk()
            ->assertSee('data-slot="card"', false)
            ->assertSee('Profile Information')
            ->assertSee('Address line 1')
            ->assertSee('Postal / ZIP code')
            ->assertSee('State / Province')
            ->assertSee('Country')
            ->assertSee('data-slot="combobox"', false)
            ->assertSee('name="country"', false)
            ->assertSee('name="state"', false)
            ->assertSee('Two Factor Authentication')
            ->assertSee('Browser Sessions')
            ->assertDontSee('card-body', false)
            ->assertDontSee('form-group', false);
    }

    public function test_profile_information_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());
        $email = $this->faker()->freeEmail();

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', [
                'name' => 'Test Name',
                'email' => $email,
                'gender' => 'male',
                'nationality' => 'nigerian',
                'country' => 'Nigeria',
                'state' => 'lagos',
                'city' => 'lagos',
                'birthday' => '2004/04/22',
                'address' => '10 Example Street',
                'address_line_2' => 'Suite 4',
                'postal_code' => '10001',
            ])
            ->call('updateProfileInformation');

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals($email, $user->fresh()->email);
        $this->assertEquals('Nigeria', $user->fresh()->country);
        $this->assertEquals('Suite 4', $user->fresh()->address_line_2);
        $this->assertEquals('10001', $user->fresh()->postal_code);
    }

    public function test_the_address_picker_dispatches_named_location_events(): void
    {
        Livewire::test(NationalityAndStateInputFields::class)
            ->set('country', '')
            ->assertDispatched('country-updated', country: null)
            ->assertDispatched('state-updated', state: null);
    }
}
