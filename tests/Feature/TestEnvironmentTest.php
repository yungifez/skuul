<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestEnvironmentTest extends TestCase
{
    public function test_suite_uses_the_testing_database(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $configuredDatabase = config('database.connections.mysql.database');

        $this->assertIsString($configuredDatabase);
        $this->assertSame($configuredDatabase, DB::scalar('select database()'));
    }
}
