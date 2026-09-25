<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportBatchRequest;
use App\Models\ImportBatch;
use App\Services\Import\CsvReader;
use App\Services\Import\ImportRegistry;
use App\Services\Import\ImportRunner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
    public function index(): View
    {
        $this->authorize('viewAny', ImportBatch::class);

        return view('pages.import.index', [
            'imports' => $this->registry->describe(),
        ]);
    }

    /**
     * Show what one import found, row by row.
     */
    public function show(ImportBatch $importBatch): View
    {
        $this->authorize('view', $importBatch);

        $importBatch->load('createdBy:id,name');

        return view('pages.import.show', ['batch' => $importBatch]);
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
}
