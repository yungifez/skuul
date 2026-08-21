<?php

namespace Tests;

use App\Models\School;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        // A request always works inside one school. Give tests the same
        // starting point, so roles and permissions resolve against a school.
        $school = School::first();

        if ($school !== null) {
            school_context()->set($school, remember: false);

            // A request always works inside one academic period too.
            academic_period_context()->resolveFor($school);
        }
    }
}
