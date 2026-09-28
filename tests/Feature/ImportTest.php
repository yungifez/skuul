<?php

namespace Tests\Feature;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
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
use RuntimeException;
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

    public function test_a_student_import_can_omit_gender(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        $runner = app(ImportRunner::class);
        $row = $this->studentRow([
            'email' => 'ada.bell@gmail.com',
            'level' => $academicLevel->name,
            'section' => $cycleSection->name,
        ]);
        unset($row['gender']);

        $batch = $runner->stage('students', [$row]);
        $runner->apply($batch);

        $enrollment = $this->enrollmentOf('ada.bell@gmail.com');

        $this->assertSame(1, $batch->fresh()->applied_count);
        $this->assertNull($enrollment->user->gender);
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

    public function test_a_student_import_refuses_a_learner_who_attends_another_school(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        $sibling = School::factory()->create(['organization_id' => $this->workingSchool()->organization_id, 'name' => 'Hill Campus']);
        $learner = User::factory()->create(['email' => 'ada.bell@gmail.com']);
        StudentRecord::factory()->create(['user_id' => $learner->id, 'school_id' => $sibling->id, 'status' => EnrollmentStatus::Active]);
        $runner = app(ImportRunner::class);

        $batch = $runner->stage('students', [
            $this->studentRow(['email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name]),
        ]);
        $runner->apply($batch);

        $this->assertSame(0, $batch->fresh()->applied_count);
        $this->assertSame(0, $this->enrollmentsOf('ada.bell@gmail.com'));
        $this->assertStringContainsString('Hill Campus', $batch->rows()->broken()->firstOrFail()->errors[0]);
    }

    public function test_a_student_import_refuses_an_admission_number_the_school_uses(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id, 'admission_number' => 'ADM/001']);
        $runner = app(ImportRunner::class);

        $batch = $runner->stage('students', [
            $this->studentRow(['email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name, 'admission_number' => 'ADM/001']),
            $this->studentRow(['email' => 'grace.ola@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name, 'admission_number' => '']),
        ]);
        $runner->apply($batch);

        $this->assertSame(1, $batch->fresh()->applied_count);
        $this->assertSame('Admission number ADM/001 is already used in this school.', $batch->rows()->broken()->firstOrFail()->errors[0]);
        $this->assertNotNull($this->enrollmentOf('grace.ola@gmail.com')->admission_number);
    }

    public function test_a_fault_while_writing_a_row_does_not_show_its_details(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        [$academicLevel, $cycleSection] = $this->levelAndSection();
        $this->mock(ChangeEnrollmentPlacement::class)
            ->shouldReceive('place')
            ->andThrow(new RuntimeException('SQLSTATE[HY000]: secret table detail'));
        $runner = app(ImportRunner::class);

        $batch = $runner->stage('students', [
            $this->studentRow(['email' => 'ada.bell@gmail.com', 'level' => $academicLevel->name, 'section' => $cycleSection->name]),
        ]);
        $runner->apply($batch);

        $this->assertSame('This row could not be written. Nothing from it was saved.', $batch->rows()->broken()->firstOrFail()->errors[0]);
        $this->assertSame(0, $this->enrollmentsOf('ada.bell@gmail.com'));
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

    public function test_a_stale_copy_of_a_written_import_is_refused(): void
    {
        $this->authorized_user(['create import', 'apply import']);
        $runner = app(ImportRunner::class);
        $batch = $runner->stage('staff', [$this->staffRow(['email' => 'ada.bell@gmail.com'])]);
        $staleCopy = ImportBatch::findOrFail($batch->id);

        $runner->apply($batch);

        $this->assertThrows(fn () => $runner->apply($staleCopy), InvalidValueException::class);
        $this->assertThrows(fn () => $runner->cancel($staleCopy), InvalidValueException::class);
        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame(ImportStatus::Applied, $batch->fresh()->status);
    }

    public function test_a_source_id_named_twice_in_one_file_is_an_error(): void
    {
        $this->authorized_user(['create import']);

        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['source_id' => 'HR-1', 'email' => 'ada.bell@gmail.com']),
            $this->staffRow(['source_id' => 'HR-1', 'email' => 'grace.ola@gmail.com']),
        ]);

        $this->assertSame(1, $batch->valid_count);
        $this->assertSame(1, $batch->invalid_count);
        $this->assertSame(['Line 2 already names this source id.'], $batch->rows()->where('line_number', 3)->sole()->errors);
    }

    public function test_a_file_saved_by_excel_reads_its_first_column(): void
    {
        $rows = app(CsvReader::class)->parse("\xEF\xBB\xBFsource_id,name\nHR-1,Ada Bell\n");

        $this->assertSame([['source_id' => 'HR-1', 'name' => 'Ada Bell']], $rows);
    }

    public function test_a_cell_typed_on_two_lines_stays_in_its_row(): void
    {
        $rows = app(CsvReader::class)->parse("name,address,email\r\nAda Bell,\"12 Marina Road\r\nLagos\",ada.bell@gmail.com\r\nGrace Ola,Abuja,grace.ola@gmail.com\r\n");

        $this->assertCount(2, $rows);
        $this->assertSame("12 Marina Road\r\nLagos", $rows[0]['address']);
        $this->assertSame('ada.bell@gmail.com', $rows[0]['email']);
        $this->assertSame('Grace Ola', $rows[1]['name']);
    }

    public function test_a_file_saved_in_the_windows_character_set_keeps_its_accents(): void
    {
        $rows = app(CsvReader::class)->parse(mb_convert_encoding("name\nRenée Adébáyò\n", 'Windows-1252', 'UTF-8'));

        $this->assertSame([['name' => 'Renée Adébáyò']], $rows);
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
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function staffRow(array $values = []): array
    {
        return $values + [
            'source_id' => null,
            'name' => 'Ada Bell',
            'email' => 'ada.bell@gmail.com',
            'birthday' => '1990-04-01',
            'gender' => 'Female',
            'staff_number' => null,
            'job_title' => 'Teacher',
            'department' => 'Science',
            'employment_type' => 'full_time',
            'joined_on' => '2024-09-01',
        ];
    }

    /**
     * Build one row of a student file.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function studentRow(array $values = []): array
    {
        return $values + [
            'source_id' => null,
            'name' => 'Ada Bell',
            'email' => 'ada.bell@gmail.com',
            'birthday' => '2012-04-01',
            'gender' => 'Female',
            'level' => 'Level one',
            'section' => 'Section one',
            'admission_number' => null,
            'admission_date' => '2024-09-01',
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
            'school_id' => $school->id,
            'academic_year_id' => current_academic_year_id(),
            'academic_level_id' => $academicLevel->id,
            'status' => AcademicStructureStatus::Active,
        ]);

        return [$academicLevel, $academicCycleSection];
    }
}
