<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Models\ImportBatch;
use App\Models\School;
use App\Models\StaffProfile;
use App\Services\Feature\FeatureManager;
use App\Services\Import\ImportRunner;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * The import screens let a person load a file, read what it will do, and
 * decide to write it.
 */
class ImportScreenTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_screen_says_which_columns_each_file_needs(): void
    {
        $this->authorized_user(['read import', 'create import']);

        $this->get(route('imports.index'))
            ->assertOk()
            ->assertSee('No imports yet')
            ->assertSee('Students')
            ->assertSee('Staff')
            ->assertSee('admission_number')
            ->assertSee('source_id');
    }

    public function test_a_checked_file_lands_on_its_own_page(): void
    {
        $this->authorized_user(['read import', 'create import']);

        $response = $this->post(route('imports.store'), [
            'type' => 'staff',
            'file' => $this->staffFile(),
        ]);

        $batch = ImportBatch::inSchool()->sole();

        $response->assertRedirect(route('imports.show', $batch));

        $this->get(route('imports.show', $batch))
            ->assertOk()
            ->assertSee('Nothing is written yet')
            ->assertSee('ada.bell@gmail.com')
            ->assertSee('not-an-email');
    }

    public function test_the_rows_say_what_is_wrong(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'not-an-email']),
        ], 'staff.csv');

        $this->get(route('imports.show', $batch))
            ->assertOk()
            ->assertSee('Has errors')
            ->assertSee('valid email address');
    }

    public function test_the_rows_can_be_narrowed_to_one_state(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
            $this->staffRow(['email' => 'not-an-email']),
        ], 'staff.csv');

        $this->get(route('imports.show', [$batch, 'state' => 'invalid']))
            ->assertOk()
            ->assertSee('not-an-email')
            ->assertDontSee('ada.bell@gmail.com');
    }

    public function test_writing_the_import_from_the_screen_saves_the_records(): void
    {
        $this->authorized_user(['read import', 'create import', 'apply import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
        ], 'staff.csv');

        $this->from(route('imports.show', $batch))
            ->post(route('imports.apply', $batch))
            ->assertRedirect(route('imports.show', $batch));

        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame(1, $batch->fresh()->applied_count);
    }

    public function test_a_person_who_may_not_write_never_sees_the_button(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
        ], 'staff.csv');

        $this->get(route('imports.show', $batch))
            ->assertOk()
            ->assertDontSee('Drop this import');
    }

    public function test_the_list_can_be_narrowed_to_one_kind_of_file(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $runner = app(ImportRunner::class);
        $staff = $runner->stage('staff', [$this->staffRow()], 'people.csv');
        $students = $runner->stage('students', [$this->studentRow()], 'learners.csv');

        $this->get(route('imports.index', ['type' => 'staff']))
            ->assertOk()
            ->assertSee(route('imports.show', $staff))
            ->assertDontSee(route('imports.show', $students));
    }

    public function test_the_screen_needs_permission(): void
    {
        $this->unauthorized_user();

        $this->get(route('imports.index'))->assertForbidden();
    }

    public function test_a_school_that_turned_imports_off_has_no_screen(): void
    {
        $this->authorized_user(['read import', 'create import']);
        app(FeatureManager::class)->disable(Feature::Imports);

        $this->get(route('imports.index'))->assertNotFound();
    }

    public function test_another_school_never_opens_the_import(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [$this->staffRow()], 'staff.csv');

        $this->authorized_user(['read import'], School::factory()->create());

        $this->get(route('imports.show', $batch))->assertForbidden();
    }

    /**
     * Build a staff file with one good row and one bad row.
     */
    private function staffFile(): UploadedFile
    {
        $csv = "source_id,name,email,birthday,gender,staff_number,job_title,department,employment_type,joined_on\n"
            ."HR-1,Ada Bell,ada.bell@gmail.com,1990-04-01,female,,Teacher,Science,full_time,2024-09-01\n"
            ."HR-2,Grace Ola,not-an-email,1991-04-01,female,,Teacher,Science,full_time,2024-09-01\n";

        return UploadedFile::fake()->createWithContent('staff.csv', $csv);
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
            'gender' => 'female',
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
            'gender' => 'female',
            'level' => 'Level one',
            'section' => 'Section one',
            'admission_number' => null,
            'admission_date' => '2024-09-01',
        ];
    }
}
