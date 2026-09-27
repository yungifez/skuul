<?php

namespace App\Livewire;

use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\ImportBatch;
use App\Services\Import\ImportRunner;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Write a checked import, or drop it. An import runs once.
 */
class ImportBatchActions extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public ImportBatch $batch;

    public function mount(ImportBatch $batch): void
    {
        Gate::authorize('view', $batch);

        $this->batch = $batch;
    }

    public function apply(ImportRunner $runner): void
    {
        Gate::authorize('apply', $this->batch);

        try {
            $batch = $runner->apply($this->batch);
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', "The import wrote {$batch->applied_count} rows.");
        $this->redirectRoute('imports.show', $batch);
    }

    public function cancel(ImportRunner $runner): void
    {
        Gate::authorize('apply', $this->batch);

        try {
            $runner->cancel($this->batch);
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', 'The import was dropped.');
        $this->redirectRoute('imports.show', $this->batch);
    }

    public function render(): View
    {
        $this->batch->refresh();

        return view('livewire.import-batch-actions');
    }
}
