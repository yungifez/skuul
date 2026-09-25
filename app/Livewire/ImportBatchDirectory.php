<?php

namespace App\Livewire;

use App\Enums\ImportStatus;
use App\Models\ImportBatch;
use App\Services\Import\ImportBatchDirectory as ImportBatchDirectoryService;
use App\Services\Import\ImportRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ImportBatchDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $status = '';

    protected ImportBatchDirectoryService $directory;

    protected ImportRegistry $registry;

    public function boot(ImportBatchDirectoryService $directory, ImportRegistry $registry): void
    {
        Gate::authorize('viewAny', ImportBatch::class);

        $this->directory = $directory;
        $this->registry = $registry;
    }

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function updatedType(): void
    {
        $this->type = $this->normalizedType($this->type);
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->status = $this->normalizedStatus($this->status);
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->type = '';
        $this->status = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $selectedStatus = ImportStatus::tryFrom($this->status);

        return ViewFactory::make('livewire.import-batch-directory', [
            'batches' => $this->directory->batches($this->type !== '' ? $this->type : null, $selectedStatus),
            'imports' => $this->registry->describe(),
            'statuses' => ImportStatus::cases(),
            'selectedStatus' => $selectedStatus,
        ]);
    }

    private function normalizeFilters(): void
    {
        $this->type = $this->normalizedType($this->type);
        $this->status = $this->normalizedStatus($this->status);
    }

    private function normalizedType(string $type): string
    {
        return array_key_exists($type, $this->registry->all()) ? $type : '';
    }

    private function normalizedStatus(string $status): string
    {
        $selectedStatus = ImportStatus::tryFrom($status);

        return $selectedStatus === null ? '' : $selectedStatus->value;
    }
}
