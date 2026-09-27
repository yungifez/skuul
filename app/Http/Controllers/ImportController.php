<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
use App\Services\Import\ImportRegistry;
use Illuminate\Contracts\View\View;

/**
 * Show the imports a school has run, and what one of them found.
 */
class ImportController extends Controller
{
    public function __construct(
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
}
