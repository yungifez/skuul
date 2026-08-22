<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestEnvironmentTest extends TestCase
{
    public function test_suite_uses_the_testing_database(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('testing', config('database.connections.mysql.database'));
        $this->assertSame('testing', DB::scalar('select database()'));
    }
}
