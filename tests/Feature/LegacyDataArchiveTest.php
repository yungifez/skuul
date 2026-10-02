<?php

namespace Tests\Feature;

use App\Models\CourseOffering;
use App\Services\Backup\LegacyDataArchive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * An upgrade that removes old academic records writes them to a file first,
 * so a school never loses its history to a migration.
 */
class LegacyDataArchiveTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = storage_path('app/legacy-v2');
        File::deleteDirectory($this->directory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_the_rows_are_written_one_per_line_before_they_go(): void
    {
        $offerings = CourseOffering::factory()->count(2)->create();

        $path = LegacyDataArchive::keep('course_offerings', DB::table('course_offerings'));

        $rows = array_map(fn (string $line): array => json_decode($line, true), file($path, FILE_IGNORE_NEW_LINES));
        $this->assertSame($offerings->pluck('id')->sort()->values()->all(), collect($rows)->pluck('id')->sort()->values()->all());
        $this->assertSame($offerings->first()->subject_id, collect($rows)->firstWhere('id', $offerings->first()->id)['subject_id']);
    }

    public function test_a_new_installation_with_nothing_to_keep_gets_no_file(): void
    {
        $this->assertNull(LegacyDataArchive::keep('course_offerings', DB::table('course_offerings')));
        $this->assertDirectoryDoesNotExist($this->directory);
    }

    public function test_a_table_that_is_already_gone_is_skipped(): void
    {
        $this->assertNull(LegacyDataArchive::keep('exam_records', DB::table('exam_records')));
    }

    public function test_running_again_adds_to_the_file_instead_of_replacing_it(): void
    {
        CourseOffering::factory()->create();

        LegacyDataArchive::keep('course_offerings', DB::table('course_offerings'));
        $path = LegacyDataArchive::keep('course_offerings', DB::table('course_offerings'));

        $this->assertCount(2, file($path, FILE_IGNORE_NEW_LINES));
    }

    public function test_the_course_offering_migration_keeps_the_offerings_it_removes(): void
    {
        $offering = CourseOffering::factory()->create();

        $migration = require database_path('migrations/2026_08_22_034429_discard_legacy_course_offering_data.php');
        $migration->up();

        $this->assertDatabaseMissing('course_offerings', ['id' => $offering->id]);
        $kept = json_decode(trim(File::get("$this->directory/course_offerings.jsonl")), true);
        $this->assertSame($offering->id, $kept['id']);
    }
}
