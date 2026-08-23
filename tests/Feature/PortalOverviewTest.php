<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Models\Organization;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Feature\FeatureManager;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A family with children at more than one campus reads them all on one page,
 * and every entry names the campus it belongs to.
 */
class PortalOverviewTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_a_learner_reads_their_own_enrollment(): void
    {
        $enrollment = $this->enrollment($this->workingSchool());

        $this->actingAs($enrollment->user)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee($this->workingSchool()->name)
            ->assertSee($enrollment->admission_number);
    }

    public function test_a_guardian_reads_both_campuses_on_one_page(): void
    {
        $organization = Organization::factory()->create();
        $first = School::factory()->create(['organization_id' => $organization->id, 'name' => 'Lagos campus']);
        $second = School::factory()->create(['organization_id' => $organization->id, 'name' => 'Abuja campus']);
        $here = $this->enrollment($first);
        $there = $this->enrollment($second);
        $guardian = $this->guardianOf($here, $there);

        $this->actingAs($guardian)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee('Lagos campus')
            ->assertSee('Abuja campus')
            ->assertSee($here->admission_number)
            ->assertSee($there->admission_number);
    }

    public function test_a_campus_that_closed_the_portal_offers_no_links(): void
    {
        $enrollment = $this->enrollment($this->workingSchool());
        app(FeatureManager::class)->disable(Feature::Portal, $this->workingSchool()->id);

        $this->actingAs($enrollment->user)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee('closed the family pages');
    }

    public function test_a_person_with_no_enrollment_has_no_overview(): void
    {
        $this->actingAs($this->memberOf($this->workingSchool()))
            ->get(route('portal.overview'))
            ->assertNotFound();
    }

    public function test_the_overview_never_shows_another_family(): void
    {
        $mine = $this->enrollment($this->workingSchool(), ['admission_number' => 'MINE/1']);
        $this->enrollment($this->workingSchool(), ['admission_number' => 'THEIRS/1']);

        $this->actingAs($mine->user)
            ->get(route('portal.overview'))
            ->assertOk()
            ->assertSee('MINE/1')
            ->assertDontSee('THEIRS/1');
    }

    /**
     * Make an enrollment at one campus.
     *
     * @param  array<string, mixed>  $values
     */
    private function enrollment(School $school, array $values = []): StudentRecord
    {
        return StudentRecord::factory()->create(['school_id' => $school->id, ...$values]);
    }

    /**
     * Make a guardian recorded against each of the given children.
     */
    private function guardianOf(StudentRecord ...$enrollments): User
    {
        $guardian = $this->memberOf($this->workingSchool());
        $guardian->parentRecord()->create(['user_id' => $guardian->id]);
        $record = $guardian->refresh()->parentRecord;

        foreach ($enrollments as $enrollment) {
            $record->students()->syncWithoutDetaching($enrollment->user);
        }

        return $guardian->fresh();
    }
}
