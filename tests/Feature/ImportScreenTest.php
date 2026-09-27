<?php

namespace Tests\Feature;

use App\Enums\Feature;
use App\Enums\ImportRowState;
use App\Enums\ImportStatus;
use App\Livewire\ImportBatchActions as ImportBatchActionsComponent;
use App\Livewire\ImportBatchDirectory as ImportBatchDirectoryComponent;
use App\Livewire\ImportFileForm as ImportFileFormComponent;
use App\Livewire\ImportRowDirectory as ImportRowDirectoryComponent;
use App\Models\ImportBatch;
use App\Models\School;
use App\Models\StaffProfile;
use App\Services\Feature\FeatureManager;
use App\Services\Import\ImportRunner;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
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

        $component = Livewire::test(ImportFileFormComponent::class)
            ->set('type', 'staff')
            ->set('file', $this->staffFile())
            ->call('save');

        $batch = ImportBatch::inSchool()->sole();

        $component->assertRedirect(route('imports.show', $batch));
        $this->assertSame(1, $batch->valid_count);
        $this->assertSame(1, $batch->invalid_count);

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

    public function test_the_row_state_filter_updates_reactively_and_normalizes_invalid_values(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
            $this->staffRow(['email' => 'not-an-email']),
        ], 'staff.csv');

        Livewire::test(ImportRowDirectoryComponent::class, ['batch' => $batch])
            ->assertSee('ada.bell@gmail.com')
            ->assertSee('not-an-email')
            ->set('state', ImportRowState::Invalid->value)
            ->assertSee('not-an-email')
            ->assertDontSee('ada.bell@gmail.com')
            ->set('state', 'not-a-state')
            ->assertSet('state', '')
            ->assertSee('ada.bell@gmail.com')
            ->call('clearFilter')
            ->assertSet('state', '')
            ->assertSee('not-an-email');
    }

    public function test_writing_the_import_from_the_screen_saves_the_records(): void
    {
        $this->authorized_user(['read import', 'create import', 'apply import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
        ], 'staff.csv');

        Livewire::test(ImportBatchActionsComponent::class, ['batch' => $batch])
            ->call('apply')
            ->assertRedirect(route('imports.show', $batch));

        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame(1, $batch->fresh()->applied_count);
    }

    public function test_a_second_click_on_a_page_left_open_writes_nothing_more(): void
    {
        $this->authorized_user(['read import', 'create import', 'apply import']);
        $batch = app(ImportRunner::class)->stage('staff', [
            $this->staffRow(['email' => 'ada.bell@gmail.com']),
        ], 'staff.csv');

        $firstTab = Livewire::test(ImportBatchActionsComponent::class, ['batch' => $batch]);
        $secondTab = Livewire::test(ImportBatchActionsComponent::class, ['batch' => $batch]);

        $firstTab->call('apply');
        $secondTab->call('apply')->assertNoRedirect();
        $secondTab->call('cancel')->assertNoRedirect();

        $this->assertSame(1, StaffProfile::inSchool()->count());
        $this->assertSame(ImportStatus::Applied, $batch->fresh()->status);
    }

    public function test_dropping_the_import_from_the_screen_writes_nothing(): void
    {
        $this->authorized_user(['read import', 'create import', 'apply import']);
        $batch = app(ImportRunner::class)->stage('staff', [$this->staffRow()], 'staff.csv');

        Livewire::test(ImportBatchActionsComponent::class, ['batch' => $batch])
            ->call('cancel')
            ->assertRedirect(route('imports.show', $batch));

        $this->assertSame(0, StaffProfile::inSchool()->count());
        $this->assertSame(ImportStatus::Cancelled, $batch->fresh()->status);
    }

    public function test_a_person_who_may_not_write_cannot_call_the_action(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $batch = app(ImportRunner::class)->stage('staff', [$this->staffRow()], 'staff.csv');

        Livewire::test(ImportBatchActionsComponent::class, ['batch' => $batch])
            ->call('apply')
            ->assertForbidden();

        $this->assertSame(ImportStatus::Checked, $batch->fresh()->status);
    }

    public function test_a_file_missing_a_column_says_so_under_the_file(): void
    {
        $this->authorized_user(['read import', 'create import']);

        Livewire::test(ImportFileFormComponent::class)
            ->set('type', 'staff')
            ->set('file', UploadedFile::fake()->createWithContent('staff.csv', "name,job_title\nAda Bell,Teacher\n"))
            ->call('save')
            ->assertHasErrors('file')
            ->assertNoRedirect();

        $this->assertSame(0, ImportBatch::inSchool()->count());
    }

    public function test_an_unknown_import_is_refused_by_the_form(): void
    {
        $this->authorized_user(['read import', 'create import']);

        Livewire::test(ImportFileFormComponent::class)
            ->set('type', 'invoices')
            ->set('file', $this->staffFile())
            ->call('save')
            ->assertHasErrors('type');

        $this->assertSame(0, ImportBatch::inSchool()->count());
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

    public function test_the_import_list_filters_reactively_clears_and_handles_invalid_values(): void
    {
        $this->authorized_user(['read import', 'create import']);
        $runner = app(ImportRunner::class);
        $staff = $runner->stage('staff', [$this->staffRow()], 'faculty.csv');
        $students = $runner->stage('students', [$this->studentRow()], 'learners.csv');

        Livewire::test(ImportBatchDirectoryComponent::class)
            ->assertSee(route('imports.show', $staff))
            ->assertSee(route('imports.show', $students))
            ->set('type', 'staff')
            ->assertSee(route('imports.show', $staff))
            ->assertDontSee(route('imports.show', $students))
            ->set('status', ImportStatus::Checked->value)
            ->assertSee(route('imports.show', $staff))
            ->set('status', ImportStatus::Failed->value)
            ->assertSee('Nothing matches this filter')
            ->set('status', 'not-a-status')
            ->assertSet('status', '')
            ->assertSee(route('imports.show', $staff))
            ->set('type', 'not-an-import')
            ->assertSet('type', '')
            ->assertSee(route('imports.show', $students))
            ->call('clearFilters')
            ->assertSet('type', '')
            ->assertSet('status', '')
            ->assertSee(route('imports.show', $staff))
            ->assertSee(route('imports.show', $students));
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
     * Build a staff file, saved by Excel, with one good row and one bad row.
     */
    private function staffFile(): UploadedFile
    {
        $csv = "\xEF\xBB\xBFsource_id,name,email,birthday,gender,staff_number,job_title,department,employment_type,joined_on\n"
            ."HR-1,Ada Bell,ada.bell@gmail.com,1990-04-01,Female,,Teacher,Science,full_time,2024-09-01\n"
            ."HR-2,Grace Ola,not-an-email,1991-04-01,Female,,Teacher,Science,full_time,2024-09-01\n";

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
}
