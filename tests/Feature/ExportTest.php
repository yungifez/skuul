<?php

namespace Tests\Feature;

use App\Actions\Finance\ChargeStudent;
use App\Actions\Report\RequestReport;
use App\Exceptions\InvalidValueException;
use App\Models\StudentRecord;
use App\Services\Print\DocumentRendererRegistry;
use App\Services\Print\PrintService;
use App\Services\Print\Renderers\BrowserRenderer;
use App\Services\Print\Renderers\DomPdfRenderer;
use App\Services\Report\ExportFormatRegistry;
use App\Traits\FeatureTestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

/**
 * A report can be handed over as a file, in more than one shape, and printed
 * by whichever renderer the installation is set up with.
 */
class ExportTest extends TestCase
{
    use FeatureTestTrait;
    use RefreshDatabase;

    public function test_the_registry_lists_the_shapes(): void
    {
        $formats = app(ExportFormatRegistry::class)->all();

        $this->assertArrayHasKey('csv', $formats);
        $this->assertArrayHasKey('xlsx', $formats);
        $this->assertArrayHasKey('pdf', $formats);
    }

    public function test_an_unknown_shape_is_refused(): void
    {
        $this->expectException(InvalidValueException::class);

        app(ExportFormatRegistry::class)->get('papyrus');
    }

    public function test_a_comma_separated_file_holds_the_headings_and_rows(): void
    {
        $csv = app(ExportFormatRegistry::class)->get('csv')->render(
            'Student balances',
            ['Admission number', 'Owed'],
            collect([['SKL/1', 500], ['SKL/2', 0]]),
        );

        $this->assertStringContainsString('Admission number', $csv);
        $this->assertStringContainsString('SKL/1,500', $csv);
    }

    public function test_a_spreadsheet_is_a_workbook_a_program_can_open(): void
    {
        $bytes = app(ExportFormatRegistry::class)->get('xlsx')->render(
            'Student balances',
            ['Admission number', 'Owed'],
            collect([['SKL/1', 500]]),
        );

        $sheet = $this->insideWorkbook($bytes, 'xl/worksheets/sheet1.xml');

        $this->assertStringContainsString('Admission number', $sheet);
        $this->assertStringContainsString('SKL/1', $sheet);
        // A number is kept as a number, so the office can total the column.
        $this->assertStringContainsString('<v>500</v>', $sheet);
    }

    public function test_a_spreadsheet_names_its_sheet_after_the_report(): void
    {
        $bytes = app(ExportFormatRegistry::class)->get('xlsx')->render(
            'Student balances',
            ['Admission number'],
            collect([['SKL/1']]),
        );

        $this->assertStringContainsString('Student balances', $this->insideWorkbook($bytes, 'xl/workbook.xml'));
    }

    public function test_a_spreadsheet_escapes_what_a_person_typed(): void
    {
        $bytes = app(ExportFormatRegistry::class)->get('xlsx')->render(
            'Class list',
            ['Name'],
            collect([['Ada & <Ben>']]),
        );

        $sheet = $this->insideWorkbook($bytes, 'xl/worksheets/sheet1.xml');

        $this->assertStringContainsString('Ada &amp; &lt;Ben&gt;', $sheet);
        $this->assertStringNotContainsString('<Ben>', $sheet);
    }

    public function test_a_spreadsheet_counts_past_the_twenty_sixth_column(): void
    {
        $columns = [];

        for ($number = 1; $number <= 28; $number++) {
            $columns[] = "Column $number";
        }

        $bytes = app(ExportFormatRegistry::class)->get('xlsx')->render('Wide', $columns, collect([]));

        $this->assertStringContainsString('r="AB1"', $this->insideWorkbook($bytes, 'xl/worksheets/sheet1.xml'));
    }

    public function test_a_report_can_be_asked_for_as_a_spreadsheet(): void
    {
        Storage::fake('local');
        $this->authorized_user(['create report']);
        $enrollment = StudentRecord::factory()->create(['school_id' => $this->workingSchool()->id]);
        app(ChargeStudent::class)->charge($enrollment, 500, 'Term one fees');

        $run = app(RequestReport::class)->request('student-balances', format: 'xlsx')->fresh();

        $this->assertSame('xlsx', $run->format);
        $this->assertStringEndsWith('.xlsx', (string) $run->file_path);
        Storage::disk('local')->assertExists($run->file_path);
    }

    public function test_a_spreadsheet_is_downloaded_as_a_spreadsheet(): void
    {
        Storage::fake('local');
        $actor = $this->authorized_user(['create report', 'read report']);
        $run = app(RequestReport::class)->request('class-list', format: 'xlsx')->fresh();

        $actor->get("/dashboard/reports/$run->id/download")
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_an_unknown_shape_is_refused_by_the_form(): void
    {
        $this->authorized_user(['create report'])
            ->post('/dashboard/reports', ['type' => 'class-list', 'format' => 'papyrus'])
            ->assertSessionHasErrors('format');
    }

    public function test_a_report_asked_for_as_a_document_is_printed(): void
    {
        Storage::fake('local');
        $this->authorized_user(['create report']);

        $run = app(RequestReport::class)->request('class-list', format: 'pdf')->fresh();

        $this->assertStringEndsWith('.pdf', (string) $run->file_path);
        $this->assertStringStartsWith('%PDF', (string) Storage::disk('local')->get($run->file_path));
    }

    public function test_the_built_in_renderer_is_used_when_nothing_is_set_up(): void
    {
        config(['services.browser_renderer.url' => null, 'services.browser_renderer.driver' => null]);

        $this->assertInstanceOf(DomPdfRenderer::class, app(DocumentRendererRegistry::class)->current());
    }

    public function test_a_browser_service_is_used_once_it_is_set_up(): void
    {
        Http::fake(['print.test/*' => Http::response('%PDF-1.7 printed', 200)]);
        config(['services.browser_renderer.url' => 'https://print.test/forms/chromium/convert/html']);

        $renderer = app(DocumentRendererRegistry::class)->current();

        $this->assertInstanceOf(BrowserRenderer::class, $renderer);
        $this->assertSame('%PDF-1.7 printed', $renderer->render('<p>Hello</p>'));
    }

    public function test_a_browser_service_that_fails_says_so(): void
    {
        Http::fake(['print.test/*' => Http::response('no', 500)]);
        config(['services.browser_renderer.url' => 'https://print.test/convert']);

        $this->expectException(InvalidValueException::class);

        app(BrowserRenderer::class)->render('<p>Hello</p>');
    }

    public function test_a_named_renderer_that_is_not_set_up_still_prints(): void
    {
        config(['services.browser_renderer.driver' => 'browser', 'services.browser_renderer.url' => null]);

        $this->assertInstanceOf(DomPdfRenderer::class, app(DocumentRendererRegistry::class)->current());
    }

    public function test_a_printed_page_comes_back_as_a_document(): void
    {
        config(['services.browser_renderer.url' => null, 'services.browser_renderer.driver' => null]);

        $response = PrintService::download('pages.report.export', [
            'title' => 'Class list',
            'columns' => ['Name'],
            'rows' => collect([['Ada']]),
        ], 'class-list');

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('class-list.pdf', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
    }

    public function test_the_reports_workspace_lists_what_was_asked_for(): void
    {
        Storage::fake('local');
        $actor = $this->authorized_user(['create report', 'read report']);
        $run = app(RequestReport::class)->request('class-list')->fresh();

        $actor->get('/dashboard/reports')
            ->assertSuccessful()
            ->assertSee('Reports and exports')
            ->assertSee((string) $run->id);
    }

    public function test_the_reports_workspace_is_closed_to_others(): void
    {
        $this->unauthorized_user()
            ->get('/dashboard/reports')
            ->assertForbidden();
    }

    /**
     * Read one part out of a workbook.
     */
    private function insideWorkbook(string $bytes, string $part): string
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($file, $bytes);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($file) === true, 'The file is not a workbook.');
        $contents = (string) $zip->getFromName($part);
        $zip->close();
        unlink($file);

        return $contents;
    }
}
