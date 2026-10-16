<?php

namespace App\Livewire;

use App\Enums\IncidentCategory;
use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Services\Discipline\IncidentDirectory as IncidentDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class IncidentDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(as: 'open', except: false)]
    public bool $openOnly = false;

    protected IncidentDirectoryService $directory;

    public function boot(IncidentDirectoryService $directory): void
    {
        Gate::authorize('viewAny', Incident::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function updatedStatus(): void
    {
        $this->status = $this->normalizedStatus($this->status);
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->category = $this->normalizedCategory($this->category);
        $this->resetPage();
    }

    public function updatedOpenOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->category = '';
        $this->openOnly = false;
        $this->resetPage();
    }

    public function render(): View
    {
        $reader = auth()->user();
        abort_unless($reader !== null, 403);
        $selectedStatus = IncidentStatus::tryFrom($this->status);
        $selectedCategory = IncidentCategory::tryFrom($this->category);

        return ViewFactory::make('livewire.incident-directory', [
            'incidents' => $this->directory->cases($reader, $selectedStatus, $selectedCategory, $this->openOnly),
            'statuses' => IncidentStatus::cases(),
            'categories' => IncidentCategory::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory,
            'openCount' => $this->directory->openCount($reader),
        ]);
    }

    private function normalizeFilters(): void
    {
        $this->status = $this->normalizedStatus($this->status);
        $this->category = $this->normalizedCategory($this->category);
    }

    private function normalizedStatus(string $status): string
    {
        $selectedStatus = IncidentStatus::tryFrom($status);

        return $selectedStatus === null ? '' : $selectedStatus->value;
    }

    private function normalizedCategory(string $category): string
    {
        $selectedCategory = IncidentCategory::tryFrom($category);

        return $selectedCategory === null ? '' : $selectedCategory->value;
    }
}
