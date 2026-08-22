<?php

namespace App\Http\Controllers;

use App\Enums\ImportStatus;
use App\Http\Requests\StoreImportBatchRequest;
use App\Models\ImportBatch;
use App\Services\Import\CsvReader;
use App\Services\Import\ImportRegistry;
use App\Services\Import\ImportRunner;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Load a file, show what it will do, then write it.
 */
class ImportController extends Controller
{
    public function __construct(
        private ImportRunner $runner,
        private CsvReader $reader,
        private ImportRegistry $registry,
    ) {}

    /**
     * Show the imports the school has run, and the way to start another.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ImportBatch::class);

        $selectedType = $request->string('type')->toString() ?: null;
        $selectedStatus = ImportStatus::tryFrom($request->string('status')->toString());

        $batches = ImportBatch::query()
            ->inSchool()
            ->with('createdBy:id,name')
            ->when($selectedType !== null, function (Builder $query) use ($selectedType): void {
                $query->where('type', $selectedType);
            })
            ->when($selectedStatus !== null, function (Builder $query) use ($selectedStatus): void {
                $query->where('status', $selectedStatus);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.import.index', [
            'batches' => $batches,
            'imports' => $this->registry->describe(),
            'statuses' => ImportStatus::cases(),
            'selectedType' => $selectedType,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    /**
     * Show what one import found, row by row.
     */
    public function show(Request $request, ImportBatch $importBatch): View
    {
        $this->authorize('view', $importBatch);

        $importBatch->load('createdBy:id,name');

        $selectedState = $request->string('state')->toString() ?: null;

        $rows = $importBatch->rows()
            ->when($selectedState !== null, function (Builder $query) use ($selectedState): void {
                $query->where('state', $selectedState);
            })
            ->paginate(50)
            ->withQueryString();

        return view('pages.import.show', [
            'batch' => $importBatch,
            'rows' => $rows,
            'columns' => $this->columnsOf($importBatch),
            'selectedState' => $selectedState,
        ]);
    }

    /**
     * Check a file and keep what it would do.
     */
    public function store(StoreImportBatchRequest $request): RedirectResponse
    {
        $this->authorize('create', ImportBatch::class);

        $file = $request->file('file');
        $rows = $this->reader->parse((string) file_get_contents($file->getRealPath()));

        $batch = $this->runner->stage(
            type: $request->string('type')->toString(),
            rows: $rows,
            sourceName: $file->getClientOriginalName(),
            actor: $request->user(),
        );

        return redirect()->route('imports.show', $batch)->with(
            'success',
            "The file was checked: $batch->valid_count rows are ready and $batch->invalid_count have errors."
        );
    }

    /**
     * Write the rows that passed the check.
     */
    public function apply(ImportBatch $importBatch): RedirectResponse
    {
        $this->authorize('apply', $importBatch);

        $batch = $this->runner->apply($importBatch);

        return back()->with('success', "The import wrote $batch->applied_count rows.");
    }

    /**
     * Drop an import without writing it.
     */
    public function cancel(ImportBatch $importBatch): RedirectResponse
    {
        $this->authorize('apply', $importBatch);

        $this->runner->cancel($importBatch);

        return back()->with('success', 'The import was dropped.');
    }

    /**
     * Get the column names the rows of this import carry.
     *
     * The rows hold whatever the file had, so the screen reads the first row
     * rather than the importer. A file with an extra column still shows it.
     *
     * @return array<int, string>
     */
    private function columnsOf(ImportBatch $batch): array
    {
        $first = $batch->rows()->first();

        return $first === null ? [] : array_keys($first->payload);
    }
}
