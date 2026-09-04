<?php

namespace Tests\Feature;

use App\Actions\Organization\GrantOrganizationMembership;
use App\Http\Middleware\SetActiveAcademicPeriod;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\School;
use App\Models\SchoolOperatingProfile;
use App\Models\User;
use App\Services\School\SchoolContext;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_view_schools_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools')
            ->assertSuccessful();
    }

    public function test_school_schema_does_not_keep_address_line_2(): void
    {
        $this->assertFalse(Schema::hasColumn('schools', 'address_line_2'));
    }

    public function test_view_schools_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/schools')
            ->assertForbidden();
    }

    public function test_create_schools_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['create school'])
            ->get('/dashboard/schools/create')
            ->assertSuccessful()
            ->assertSee('Address *')
            ->assertDontSee('Address line 2')
            ->assertSee('data-slot="combobox"', false)
            ->assertSee('Postal / ZIP code');
    }

    public function test_create_schools_cannot_be_rendered_to_unauthorized_user()
    {
        $this->unauthorized_user()
            ->get('/dashboard/schools/create')
            ->assertForbidden();
    }

    public function test_user_can_create_school()
    {
        // A campus belongs to an organization, so the person needs scope in
        // the organization they are adding it to.
        $organization = Organization::factory()->create();
        $this->authorized_user(['create school']);
        app(GrantOrganizationMembership::class)->grant(auth()->user(), $organization);

        $this->post('/dashboard/schools', [
            'organization_id' => $organization->id,
            'name' => 'Test school',
            'address' => 'Test address',
            'country' => 'Canada',
            'state' => 'British Columbia',
            'city' => 'Vancouver',
            'postal_code' => 'V6B 1A1',
            'phone' => '+123 456789',
            'email' => 'test@email.com',
            'initials' => 'TS',
        ]);

        $this->assertDatabaseHas('schools', [
            'organization_id' => $organization->id,
            'name' => 'Test school',
            'address' => 'Test address',
            'country' => 'Canada',
            'state' => 'British Columbia',
            'city' => 'Vancouver',
            'postal_code' => 'V6B 1A1',
            'phone' => '+123 456789',
            'email' => 'test@email.com',
            'initials' => 'TS',
        ]);
    }

    public function test_unauthorized_user_can_not_create_school()
    {
        $this->unauthorized_user()
            ->post('/dashboard/schools', ['name' => 'Test school', 'address' => 'Test address', 'phone' => 'Test phone', 'email' => 'test@email.com', 'initials' => 'TS'])
            ->assertForbidden();
    }

    public function test_show_school_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools/1')
            ->assertSuccessful()
            ->assertSee('Core details and the academic workspace this school is currently using.')
            ->assertSee('Academic workspace')
            ->assertSee('Contact details');
    }

    public function test_show_school_can_be_rendered_to_authorized_user_in_same_school()
    {
        $this->authorized_user(['read school'])
            ->get('/dashboard/schools/1')
            ->assertSuccessful();
    }

    public function test_edit_school_can_be_rendered_to_authorized_user()
    {
        $this->authorized_user(['update school'])
            ->get('/dashboard/schools/1/edit')
            ->assertSuccessful()
            ->assertSee('Edit school details')
            ->assertSee('Basic details')
            ->assertSee('School logo')
            ->assertSee('Address *')
            ->assertDontSee('Address line 2')
            ->assertSee('data-slot="combobox"', false)
            ->assertSee('Postal / ZIP code');
    }

    public function test_missing_school_logo_falls_back_to_the_application_logo(): void
    {
        Storage::fake('public');
        $school = $this->workingSchool();
        $school->update(['logo_path' => 'schools/missing-logo.png']);

        $this->assertSame(asset(config('app.logo')), $school->fresh()->logo_url);
    }

    public function test_the_application_logo_has_a_deployable_default(): void
    {
        $this->assertSame('img/logo/logo.png', config('app.logo'));
        $this->assertFileExists(public_path(config('app.logo')));
    }

    public function test_school_setup_can_be_rendered_for_the_current_school()
    {
        $school = $this->workingSchool();
        $school->update([
            'academic_year_id' => null,
            'academic_period_id' => null,
        ]);

        $this->withoutMiddleware(SetActiveAcademicPeriod::class);
        academic_period_context()->forget();

        $this->authorized_user(['manage school settings'], $school)
            ->get('/dashboard/schools/settings')
            ->assertSuccessful()
            ->assertSee('Set up your school')
            ->assertSee('School setup checklist')
            ->assertSee('How teaching works')
            ->assertSee('Classes')
            ->assertSee('required steps remain')
            ->assertSee('No current school year is selected.')
            ->assertSee('aria-label="School calendar help"', false)
            ->assertSee('aria-label="Classes help"', false);
    }

    public function test_school_administrator_sees_setup_guidance_on_the_dashboard(): void
    {
        $school = $this->workingSchool();
        $school->update([
            'academic_year_id' => null,
            'academic_period_id' => null,
        ]);

        $this->withoutMiddleware(SetActiveAcademicPeriod::class);
        academic_period_context()->forget();

        $response = $this->platform_admin($school)
            ->get('/dashboard')
            ->assertSuccessful()
            ->assertSee('Finish setting up your school')
            ->assertSee('Next priority')
            ->assertSee('View all setup steps');

        $content = $response->getContent();
        $setupPosition = strpos($content, 'Finish setting up your school');
        $schoolOverviewPosition = strpos($content, 'Your school, ready for the day');
        $workingSchoolPosition = strpos($content, 'You are working in');

        $this->assertNotFalse($setupPosition);
        $this->assertNotFalse($schoolOverviewPosition);
        $this->assertNotFalse($workingSchoolPosition);
        $this->assertLessThan($schoolOverviewPosition, $setupPosition);
        $this->assertLessThan($workingSchoolPosition, $schoolOverviewPosition);
    }

    public function test_dashboard_links_to_create_the_first_academic_year_when_none_exists(): void
    {
        $school = School::factory()->create();
        $school->update([
            'setup_details_completed_at' => now(),
            'country' => 'Canada',
            'state' => 'British Columbia',
            'city' => 'Vancouver',
            'postal_code' => 'V6B 1A1',
        ]);

        $this->withoutMiddleware(SetActiveAcademicPeriod::class);
        academic_period_context()->forget();

        $this->platform_admin($school)
            ->get('/dashboard')
            ->assertSuccessful()
            ->assertSee('Create first school year')
            ->assertSee(route('academic-years.create', ['setup' => 1]), false);

        $this->assertFalse(AcademicYear::query()->where('school_id', $school->id)->exists());
    }

    public function test_a_school_can_save_its_familiar_operating_language(): void
    {
        $school = $this->workingSchool();
        $this->authorized_user(['manage school settings'], $school)
            ->put('/dashboard/schools/operating-profile', [
                'preset' => 'subject_schedule',
                'labels' => ['academic_year' => 'Academic year', 'class_level' => 'Form', 'section' => 'Stream', 'period' => 'Semester', 'course' => 'Course', 'fee' => 'Tuition', 'homeroom_teacher' => 'Form teacher'],
            ])
            ->assertRedirect('/dashboard/schools/settings');

        $this->assertDatabaseHas('school_operating_profiles', ['school_id' => $school->id, 'preset' => 'subject_schedule']);
        $this->assertSame('Form teacher', SchoolOperatingProfile::query()->where('school_id', $school->id)->firstOrFail()->labels['homeroom_teacher']);

    }

    public function test_school_language_starts_with_one_explicit_default_pattern(): void
    {
        $school = $this->workingSchool();

        $response = $this->authorized_user(['manage school settings'], $school)
            ->get(route('schools.operating-profile.edit'))
            ->assertSuccessful()
            ->assertSee('Starting language pattern')
            ->assertSee('Class-based school')
            ->assertSee('Grade and subject-based school')
            ->assertSee('Mixed class and subject school')
            ->assertSee('Default')
            ->assertSee('Every option includes the same seven labels');

        $this->assertMatchesRegularExpression('/name="preset" value="home_sections"[^>]*checked/', $response->getContent());
    }

    public function test_school_terminology_is_rendered_on_the_school_setup_screen(): void
    {
        $school = $this->workingSchool();
        SchoolOperatingProfile::query()->updateOrCreate(
            ['school_id' => $school->id],
            [
                'preset' => 'subject_schedule',
                'labels' => ['academic_year' => 'Academic year', 'class_level' => 'Form', 'section' => 'Stream', 'period' => 'Semester', 'course' => 'Course', 'fee' => 'Tuition', 'homeroom_teacher' => 'Form teacher'],
            ],
        );

        $this->authorized_user(['manage school settings'], $school)
            ->get('/dashboard/schools/settings')
            ->assertSuccessful()
            ->assertSee('Forms')
            ->assertSee('Streams this academic year');
    }

    public function test_unauthorized_user_cannot_update_school()
    {
        $this->unauthorized_user()
            ->put('/dashboard/schools/1', ['name' => 'Test school', 'address' => 'Test address', 'phone' => 'Test phone', 'email' => 'test@email.com', 'initials' => 'TS'])
            ->assertForbidden();
    }

    public function test_authorized_user_can_update_school()
    {
        $school = $this->workingSchool();
        $this->authorized_user(['update school'], $school)
            ->patch("/dashboard/schools/$school->id", [
                'name' => 'Test school 2',
                'address' => 'something street',
                'country' => 'Canada',
                'state' => 'Ontario',
                'city' => 'Toronto',
                'postal_code' => 'M5V 2T6',
                'initials' => 'TS2',
                'phone' => '123456789',
                'email' => 'school@test.com',
            ]);

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'Test school 2',
            'address' => 'something street',
            'country' => 'Canada',
            'state' => 'Ontario',
            'city' => 'Toronto',
            'postal_code' => 'M5V 2T6',
            'initials' => 'TS2',
            'phone' => '123456789',
            'email' => 'school@test.com',
        ]);
    }

    public function test_a_school_member_cannot_update_another_school()
    {
        $school = School::factory()->create(['name' => 'Other school']);

        $this->authorized_user(['update school'])
            ->patch("/dashboard/schools/$school->id", [
                'name' => 'Changed school',
                'address' => 'changed address',
                'initials' => 'CS',
                'phone' => '123456789',
                'email' => 'changed@example.com',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'Other school',
        ]);
    }

    public function test_that_unauthorized_user_cannot_delete_school()
    {
        $school = School::factory()->create();
        $this->unauthorized_user()
            ->delete("/dashboard/schools/$school->id")
            ->assertForbidden();
    }

    public function test_that_unauthorized_user_cannot_delete_school_if_it_is_their_current_school()
    {
        $this->authorized_user(['delete school'])
            ->delete('/dashboard/schools/1');

        $this->assertDatabaseHas('schools', [
            'id' => 1,
        ]);
    }

    public function test_user_cannot_delete_school_with_users_in_it()
    {
        $school = School::factory()->create();
        $this->memberOf($school, User::factory()->create());

        $this->authorized_user(['delete school'], $school)
            ->delete("/dashboard/schools/$school->id");

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
        ]);
    }

    public function test_user_can_delete_school_with_no_users()
    {
        // A school with no users has nobody who could hold a permission in it,
        // so only a platform administrator can empty one out.
        $school = School::factory()->create();

        $this->platform_admin()
            ->delete("/dashboard/schools/$school->id");

        $this->assertModelMissing($school);
    }

    public function test_a_school_member_cannot_delete_another_school()
    {
        $school = School::factory()->create();

        $this->authorized_user(['delete school'])
            ->delete("/dashboard/schools/$school->id")
            ->assertForbidden();

        $this->assertModelExists($school);
    }

    public function test_platform_admin_can_set_the_working_school()
    {
        $user = User::where('email', 'super@example.com')->first();
        // since factory produces random password, it had to be changed
        $user->password = Hash::make('random-password-lolololololol');
        $user->save();

        $this->actingAs($user);
        $school = School::factory()->create();

        $this->post('/dashboard/schools/set-school', ['school_id' => $school->id])
            ->assertSessionHas(SchoolContext::SESSION_KEY, $school->id);

        // Switching school must never write to the person's record.
        $this->assertSame(1, $user->fresh()->schoolMemberships()->count());
    }

    public function test_a_person_cannot_set_a_school_they_do_not_belong_to()
    {
        $otherSchool = School::factory()->create();

        $this->authorized_user(['read school'])
            ->post('/dashboard/schools/set-school', ['school_id' => $otherSchool->id])
            ->assertForbidden();
    }

    public function test_sidebar_shows_the_school_switcher_for_people_with_multiple_schools(): void
    {
        $school = $this->workingSchool();
        $otherSchool = School::factory()->create(['name' => 'Second Campus']);
        $user = User::factory()->create();
        school_context()->set($school, remember: false);
        $user->givePermissionTo('read school');
        $this->memberOf($school, $user);
        $this->memberOf($otherSchool, $user);
        $this->actingAsMemberOf($school, $user);

        $this->get('/dashboard/schools')
            ->assertSuccessful()
            ->assertSee('sidebar-school-switcher', false)
            ->assertSee('Second Campus');
    }
}
