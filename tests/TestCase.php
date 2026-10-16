<?php

namespace Tests;

use App\Models\FinancialPeriod;
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

            $startsOn = now()->startOfYear()->toDateString();
            $endsOn = now()->endOfYear()->toDateString();

            FinancialPeriod::query()->firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => 'Current finance period',
                ],
                [
                    'starts_on' => $startsOn,
                    'ends_on' => $endsOn,
                ],
            );

            // A request always works inside one academic period too.
            academic_period_context()->resolveFor($school);
        }
    }
}
