<?php

namespace Tests\Feature;

use App\Livewire\NoticeEmailPreferences;
use App\Models\NoticeNotificationPreference;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalNotificationPreferenceTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_guardian_can_manage_notice_email_choices_for_each_campus(): void
    {
        $first = $this->enrollment($this->workingSchool());
        $secondSchool = School::factory()->create(['name' => 'Second campus']);
        $second = $this->enrollment($secondSchool);
        $guardian = $this->guardianOf($first, $second);

        $this->actingAs($guardian)
            ->get(route('portal.notification-preferences.edit'))
            ->assertOk()
            ->assertSee($this->workingSchool()->name)
            ->assertSee('Second campus');

        Livewire::actingAs($guardian)
            ->test(NoticeEmailPreferences::class, ['isPortal' => true])
            ->assertSet("emailEnabled.{$first->school_id}", true)
            ->set("emailEnabled.{$first->school_id}", false)
            ->assertDispatched('status-message', type: 'success')
            ->set("emailEnabled.{$first->school_id}", false)
            ->assertSee('Notices only here');

        $this->assertDatabaseHas('notice_notification_preferences', [
            'user_id' => $guardian->id,
            'school_id' => $first->school_id,
            'email_enabled' => false,
        ]);
        $this->assertSame(1, NoticeNotificationPreference::query()->where('user_id', $guardian->id)->count());

        Livewire::actingAs($guardian)
            ->test(NoticeEmailPreferences::class, ['isPortal' => true])
            ->assertSet("emailEnabled.{$first->school_id}", false)
            ->assertSet("emailEnabled.{$second->school_id}", true);
    }

    public function test_a_guardian_cannot_change_a_campus_outside_their_family(): void
    {
        $enrollment = $this->enrollment($this->workingSchool());
        $otherSchool = School::factory()->create();
        $otherEnrollment = $this->enrollment($otherSchool);
        $guardian = $this->guardianOf($enrollment);

        Livewire::actingAs($guardian)
            ->test(NoticeEmailPreferences::class, ['isPortal' => true])
            ->set("emailEnabled.{$otherEnrollment->school_id}", false)
            ->assertDispatched('status-message', type: 'danger', message: 'You can only change the setting for your own schools.');

        $this->assertDatabaseMissing('notice_notification_preferences', [
            'user_id' => $guardian->id,
            'school_id' => $otherEnrollment->school_id,
        ]);
    }

    public function test_a_person_without_a_portal_enrollment_cannot_open_notification_settings(): void
    {
        $person = $this->memberOf($this->workingSchool());

        $this->actingAs($person)
            ->get(route('portal.notification-preferences.edit'))
            ->assertNotFound();

        Livewire::actingAs($person)->test(NoticeEmailPreferences::class, ['isPortal' => true])->assertNotFound();
    }

    /**
     * Create an enrollment at the given campus.
     */
    private function enrollment(School $school): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $school->id]);
    }

    /**
     * Make a guardian recorded against each of the given learners.
     */
    private function guardianOf(StudentRecord ...$enrollments): User
    {
        $guardian = User::factory()->create();
        $parentRecord = $guardian->parentRecord()->create(['user_id' => $guardian->id]);

        foreach ($enrollments as $enrollment) {
            $parentRecord->students()->syncWithoutDetaching($enrollment->user);
        }

        return $guardian->fresh();
    }
}
