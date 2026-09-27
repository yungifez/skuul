<?php

namespace App\Livewire;

use App\Exceptions\InvalidValueException;
use App\Models\ImportBatch;
use App\Services\Import\CsvReader;
use App\Services\Import\ImportRegistry;
use App\Services\Import\ImportRunner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Load a file and check it. Nothing is written until the school reads the check.
 */
class ImportFileForm extends Component
{
    use WithFileUploads;

    public string $type = '';

    /** @var UploadedFile|null */
    public $file = null;

    public function mount(): void
    {
        Gate::authorize('create', ImportBatch::class);
    }

    public function save(ImportRegistry $registry, CsvReader $reader, ImportRunner $runner): void
    {
        Gate::authorize('create', ImportBatch::class);

        $this->validate([
            'type' => ['required', Rule::in(array_keys($registry->all()))],
            'file' => ['required', 'file', 'mimetypes:text/plain,text/csv,application/csv', 'max:5120'],
        ], [], ['type' => 'import', 'file' => 'CSV file']);

        try {
            $batch = $runner->stage(
                type: $this->type,
                rows: $reader->parse((string) file_get_contents($this->file->getRealPath())),
                sourceName: $this->file->getClientOriginalName(),
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('file', $exception->getMessage());

            return;
        }

        session()->flash('success', "The file was checked: {$batch->valid_count} rows are ready and {$batch->invalid_count} have errors.");
        $this->redirectRoute('imports.show', $batch);
    }

    public function render(ImportRegistry $registry): View
    {
        return view('livewire.import-file-form', ['imports' => $registry->describe()]);
    }
}
