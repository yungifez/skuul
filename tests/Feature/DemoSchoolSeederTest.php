<?php

namespace Tests\Feature;

use App\Enums\AcademicPeriodStatus;
use App\Models\CourseOffering;
use App\Models\Organization;
use App\Models\StudentRecord;
use App\Models\User;
use Database\Seeders\Demo\DemoSchool;
use Database\Seeders\DemoSchoolSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSchoolSeederTest extends TestCase
{
    use RefreshDatabase;

    private DemoSchool $demo;

    protected function setUp(): void
    {
        parent::setUp();

        $seeder = new DemoSchoolSeeder;
        Model::unguarded(fn () => $seeder->run());
        $this->demo = $seeder->demoSchool();
    }

    public function test_it_builds_a_district_with_two_readable_campuses(): void
    {
        $organization = Organization::query()->where('name', 'Riverside Unified School District')->sole();

        $this->assertSame(
            ['Maple Grove Middle School', 'Riverside High School'],
            $organization->schools()->orderBy('name')->pluck('name')->all(),
        );
    }

    public function test_every_demo_account_signs_in_with_the_demo_password(): void
    {
        foreach (config('demo.accounts') as $role => $email) {
            $account = User::query()->where('email', $email)->first();

            $this->assertNotNull($account, "Missing demo account: $role");
            $this->assertTrue(Hash::check(config('demo.password'), $account->password), "Wrong password: $role");
        }
    }

    public function test_the_school_year_runs_today_with_two_semesters(): void
    {
        $year = $this->demo->academicYear;

        $this->assertTrue(now()->between($year->starts_on, $year->ends_on->endOfDay()));
        $this->assertSame(['Fall semester', 'Spring semester'], array_keys($this->demo->periods));
        $this->assertSame($year->id, $this->demo->campus->fresh()->academic_year_id);
        $this->assertNotSame(AcademicPeriodStatus::Draft, $year->status);
    }

    public function test_learners_fill_named_sections_with_linked_guardians(): void
    {
        $this->assertSame(['9A', '9B', '10A', '10B', '11A', '11B', '12A', '12B'], array_keys($this->demo->sections));
        $this->assertCount(40, $this->demo->allStudents());
        $this->assertSame(40, StudentRecord::query()->where('school_id', $this->demo->campus->id)->count());
        $this->assertSame('Ethan Brooks', $this->demo->demoStudent->name);
        $this->assertTrue($this->demo->demoParent->parentRecord->students->contains($this->demo->demoStudent));
    }

    public function test_every_subject_is_taught_to_every_section_by_a_named_teacher(): void
    {
        $this->assertCount(7 * 8, $this->demo->offerings);
        $this->assertSame(
            7 * 8,
            CourseOffering::query()->whereKey(collect($this->demo->offerings)->pluck('id'))->whereHas('teachingAssignments')->count(),
        );
    }

    public function test_no_record_carries_placeholder_text(): void
    {
        $names = collect($this->demo->allStudents())->merge($this->demo->guardians)->merge($this->demo->teachers)->pluck('name');

        $this->assertSame($names->count(), $names->unique()->count(), 'Two demo people share a name.');
        $this->assertFalse($names->contains(fn (string $name): bool => str_contains($name, 'Doe')));
    }
}
