<?php

namespace Tests\Feature;

use App\Enums\AcademicStructureStatus;
use App\Enums\ImportRowState;
use App\Enums\ImportStatus;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\ImportBatch;
use App\Models\ImportedRecord;
use App\Models\School;
use App\Models\StaffProfile;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Import\CsvReader;
use App\Services\Import\ImportRegistry;
use App\Services\Import\ImportRunner;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * A file is checked before it is written, and writing it twice changes the
 * same records instead of copying them.
 */
class ImportTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_registry_lists_the_imports(): void
    {
        $imports = app(ImportRegistry::class)->all();

        $this->assertArrayHasKey('students', $imports);
        $this->assertArrayHasKey('staff', $imports);
    }

    public function test_an_unknown_import_is_refused(): void
    {
        $this->expectException(InvalidValueException::class);

        app(ImportRegistry::class)->get('nothing-like-this');
    }

    public function test_a_csv_is_read_by_its_column_names(): void
    {
        $rows = app(CsvReader::class)->parse("Name , Email\nAda Bell,ada.bell@gmail.com\n\nGrace Ola,grace.ola@gmail.com\n");

        $this->assertCount(2, $rows);
        $this->assertSame('Ada Bell', $rows[0]['name']);
        $this->assertSame('grace.ola@gmail.com', $rows[1]['email']);
    }

    public function test_a_file_without_a_heading_row_is_refused(): void
    {
        $this->expectException(InvalidValueException::class);

        app(CsvReader::class)->parse('   ');
    }

    public function test_a_file_missing_a_column_is_refused(): void
    {
        $this->authorized_user(['create import']);

        $this->expectException(InvalidValueException::class);

        app(ImportRunner::class)->stage('staff', [['name' => 'Ada Bell']]);
    }

    public function test_checking_a_file_writes_nothing(): void
    {
        $this->authorized_user(['create import']);

        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
            $this->staffRow(['email' => 'not-an-email']),
        ], 'staff.csv');

        $this->assertSame(ImportStatus::Checked, $batch->status);
        $this->assertSame(2, $batch->row_count);
        $this->assertSame(1, $batch->valid_count);
        $this->assertSame(1, $batch->invalid_count);
        $this->assertSame(0, StaffProfile::count());
        $this->assertSame(0, User::where('email', 'ada.bell@gmail.com')->count());
    }

    public function test_a_bad_row_says_what_is_wrong_and_names_its_line(): void
    {
        $this->authorized_user(['create import']);

        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'not-an-email']),
        ]);

        $row = $batch->rows()->firstOrFail();

        $this->assertSame(ImportRowState::Invalid, $row->state);
        $this->assertSame(2, $row->line_number);
        $this->assertNotEmpty($row->errors);
    }

    public function test_only_the_good_rows_are_written(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        $runner = app(ImportRunner::class);

        $batch = $runner->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com', 'name' => 'Ada Bell']),
            $this->staffRow(['email' => 'not-an-email']),
        ]);

        $runner->apply($batch);

        $this->assertSame(ImportStatus::Applied, $batch->fresh()->status);
        $this->assertSame(1, $batch->fresh()->applied_count);
        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame('Ada Bell', User::where('email', 'ada.bell@gmail.com')->firstOrFail()->name);
    }

    public function test_the_same_file_can_be_imported_twice(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        $runner = app(ImportRunner::class);
        $rows = [$this->staffRow(['source_id' => 'HR-1', 'email' => 'ada.bell@gmail.com', 'job_title' => 'Teacher'])];

        $runner->apply($runner->stage('staff', $rows));
        $rows[0]['job_title'] = 'Head of year';
        $runner->apply($runner->stage('staff', $rows));

        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame('Head of year', StaffProfile::inSchool()->firstOrFail()->job_title);
        $this->assertSame(1, ImportedRecord::where('type', 'staff')->where('source_id', 'HR-1')->count());
    }

    public function test_a_row_that_fails_while_writing_keeps_the_others(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        $runner = app(ImportRunner::class);

        $batch = $runner->stage('students', [
            $this->studentRow(['email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name]),
            $this->studentRow(['email' => 'grace.ola@gmail.com', 'level' => 'A level that does not exist']),
        ]);

        $runner->apply($batch);

        $this->assertSame(1, $batch->fresh()->applied_count);
        $this->assertSame(1, $this->enrollmentsOf('ada.bell@gmail.com'));
        $this->assertSame(0, $this->enrollmentsOf('grace.ola@gmail.com'));
        $this->assertStringContainsString(
            'A level that does not exist',
            $batch->rows()->broken()->firstOrFail()->errors[0]
        );
    }

    public function test_a_student_import_places_the_student(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        $runner = app(ImportRunner::class);

        $runner->apply($runner->stage('students', [
            $this->studentRow(['email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name]),
        ]));

        $enrollment = $this->enrollmentOf('ada.bell@gmail.com');

        $this->assertSame($cycleSection->id, $enrollment->academic_cycle_section_id);
        $this->assertSame(1, $enrollment->placements()->count());
        $this->assertTrue($enrollment->user->hasRole('student'));
    }

    public function test_a_student_import_never_makes_a_second_enrollment(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        [$otherLevel, $otherSection] = $this->levelAndSection();
        $runner = app(ImportRunner::class);
        $row = $this->studentRow(['source_id' => 'SIS-1', 'email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name]);

        $runner->apply($runner->stage('students', [$row]));
        $row['level'] = $otherLevel->name;
        $row['section'] = $otherSection->name;
        $runner->apply($runner->stage('students', [$row]));

        $this->assertSame(1, $this->enrollmentsOf('ada.bell@gmail.com'));

        $enrollment = $this->enrollmentOf('ada.bell@gmail.com');

        $this->assertSame($otherSection->id, $enrollment->academic_cycle_section_id);
        $this->assertSame(2, $enrollment->placements()->count());
    }

    public function test_an_import_is_written_once(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        $runner = app(ImportRunner::class);
        $batch = $runner->stage('staff', [$this->staffRow(['email' => 'ada.bell@gmail.com'])]);
        $runner->apply($batch);

        $this->expectException(InvalidValueException::class);

        $runner->apply($batch);
    }

    public function test_a_dropped_import_writes_nothing(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        $runner = app(ImportRunner::class);
        $batch = $runner->stage('staff', [$this->staffRow(['email' => 'ada.bell@gmail.com'])]);

        $runner->cancel($batch);

        $this->assertSame(ImportStatus::Cancelled, $batch->fresh()->status);

        $this->expectException(InvalidValueException::class);

        $runner->apply($batch->fresh());
    }

    public function test_another_school_never_reads_the_import(): void
    {
        $this->authorized_user(['create import', 'read import']);
        $batch = app(ImportRunner::class)->stage('staff', [$this->staffRow(['email' => 'ada.bell@gmail.com'])]);

        $this->authorized_user(['read import', 'apply import'], School::factory()->create());

        $this->assertFalse(Gate::forUser(auth()->user())->allows('view', $batch));
        $this->assertFalse(Gate::forUser(auth()->user())->allows('apply', $batch));
    }

    public function test_writing_an_import_needs_its_own_permission(): void
    {
        $this->authorized_user(['create import', 'read import']);
        $batch = ImportBatch::create(['school_id' => $this->workingSchool()->id, 'type' => 'staff']);

        $this->assertTrue(Gate::forUser(auth()->user())->allows('view', $batch));
        $this->assertFalse(Gate::forUser(auth()->user())->allows('apply', $batch));
    }

    /**
     * Count the enrollments of one person in the working school.
     */
    private function enrollmentsOf(string $email): int
    {
        return StudentRecord::inSchool()
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->count();
    }

    /**
     * Get the enrollment of one person in the working school.
     */
    private function enrollmentOf(string $email): StudentRecord
    {
        return StudentRecord::inSchool()
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->firstOrFail();
    }

    /**
     * Build one row of a staff file.
     *
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function staffRow(array $values = []): array
    {
        return $values + [
            'source_id'       => null,
            'name'            => 'Ada Bell',
            'email'           => 'ada.bell@gmail.com',
            'birthday'        => '1990-04-01',
            'gender'          => 'female',
            'staff_number'    => null,
            'job_title'       => 'Teacher',
            'department'      => 'Science',
            'employment_type' => 'full_time',
            'joined_on'       => '2024-09-01',
        ];
    }

    /**
     * Build one row of a student file.
     *
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function studentRow(array $values = []): array
    {
        return $values + [
            'source_id'        => null,
            'name'             => 'Ada Bell',
            'email'            => 'ada.bell@gmail.com',
            'birthday'         => '2012-04-01',
            'gender'           => 'female',
            'level'            => 'Level one',
            'section'          => 'Section one',
            'admission_number' => null,
            'admission_date'   => '2024-09-01',
        ];
    }

    /**
     * Make an academic level with one active cycle section in the working school.
     *
     * @return array{0: AcademicLevel, 1: AcademicCycleSection}
     */
    private function levelAndSection(): array
    {
        $school = $this->workingSchool();
        $academicLevel = AcademicLevel::factory()->create(['school_id' => $school->id]);
        $academicCycleSection = AcademicCycleSection::factory()->create([
            'school_id'         => $school->id,
            'academic_year_id'  => current_academic_year_id(),
            'academic_level_id' => $academicLevel->id,
            'status'            => AcademicStructureStatus::Active,
        ]);

        return [$academicLevel, $academicCycleSection];
    }
}
