<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StudentRecord;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An enrollment the upgrade could not place at a campus is placed when the learner has one.
 */
class EnrollmentCampusBackfillTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_an_enrollment_goes_to_the_only_campus_of_its_learner(): void
    {
        $campus = School::factory()->create();
        $enrollment = $this->unplacedEnrollmentAt([$campus]);

        $this->runBackfill();

        $this->assertSame($campus->id, $enrollment->fresh()->school_id);
    }

    public function test_an_enrollment_stays_unplaced_when_the_campus_is_not_clear(): void
    {
        $twoCampuses = $this->unplacedEnrollmentAt([School::factory()->create(), School::factory()->create()]);
        $noCampus = $this->unplacedEnrollmentAt([]);

        $this->runBackfill();

        $this->assertNull($twoCampuses->fresh()->school_id);
        $this->assertNull($noCampus->fresh()->school_id);
    }

    public function test_an_admission_number_the_campus_already_uses_is_left_for_staff(): void
    {
        $campus = School::factory()->create();
        StudentRecord::factory()->create(['school_id' => $campus->id, 'admission_number' => 'A-100']);
        $enrollment = $this->unplacedEnrollmentAt([$campus], 'A-100');

        $this->runBackfill();

        $this->assertNull($enrollment->fresh()->school_id);
    }

    /**
     * @param  array<int, School>  $campuses
     */
    private function unplacedEnrollmentAt(array $campuses, ?string $admissionNumber = null): StudentRecord
    {
        $enrollment = StudentRecord::factory()->create(['admission_number' => $admissionNumber ?? fake()->unique()->bothify('####????')]);
        $enrollment->user->schoolMemberships()->delete();

        foreach ($campuses as $campus) {
            $this->memberOf($campus, $enrollment->user);
        }

        $enrollment->forceFill(['school_id' => null])->save();

        return $enrollment;
    }

    private function runBackfill(): void
    {
        $migration = require database_path('migrations/2026_09_28_140954_place_enrollments_the_upgrade_left_without_a_campus.php');
        $migration->up();
    }
}
