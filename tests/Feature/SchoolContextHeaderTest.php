<?php

namespace Tests\Feature;

use App\Livewire\Layouts\Header;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolContextHeaderTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_header_names_the_working_school_for_a_platform_admin()
    {
        $school = $this->workingSchool();

        $this->platform_admin($school);

        Livewire::test(Header::class)
            ->assertSee('You are currently on')
            ->assertSee($school->name);
    }

    public function test_header_hides_the_school_chip_from_a_single_school_member()
    {
        $school = $this->workingSchool();

        $this->unauthorized_user($school);

        Livewire::test(Header::class)
            ->assertDontSee('You are currently on')
            ->assertDontSee($school->name);
    }

    public function test_header_prompts_a_platform_admin_with_no_working_school()
    {
        $school = $this->workingSchool();

        $this->platform_admin($school);

        school_context()->forget();

        Livewire::test(Header::class)
            ->assertSee('Set a school');
    }
}
