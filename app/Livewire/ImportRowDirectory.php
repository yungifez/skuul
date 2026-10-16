<?php

namespace App\Livewire;

use App\Enums\ImportRowState;
use App\Models\ImportBatch;
use App\Services\Import\ImportRowDirectory as ImportRowDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ImportRowDirectory extends Component
{
    use WithPagination;

    #[Locked]
    public ImportBatch $batch;

    #[Url(except: '')]
    public string $state = '';

    protected ImportRowDirectoryService $directory;

    public function boot(ImportRowDirectoryService $directory): void
    {
        $this->directory = $directory;
    }

    public function mount(ImportBatch $batch): void
    {
        $this->batch = $batch;
        Gate::authorize('view', $this->batch);
        $this->normalizeState();
    }

    public function updatedState(): void
    {
        $this->normalizeState();
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->state = '';
        $this->resetPage();
    }

    public function render(): View
    {
        Gate::authorize('view', $this->batch);
        $selectedState = ImportRowState::tryFrom($this->state);

        return ViewFactory::make('livewire.import-row-directory', [
            'rows' => $this->directory->rows($this->batch, $selectedState),
            'columns' => $this->directory->columns($this->batch),
            'states' => ImportRowState::cases(),
            'selectedState' => $selectedState,
        ]);
    }

    private function normalizeState(): void
    {
        $selectedState = ImportRowState::tryFrom($this->state);
        $this->state = $selectedState === null ? '' : $selectedState->value;
    }
}
