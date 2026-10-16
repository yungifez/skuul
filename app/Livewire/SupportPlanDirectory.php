<?php

namespace App\Livewire;

use App\Enums\SupportCategory;
use App\Enums\SupportPlanStatus;
use App\Models\SupportPlan;
use App\Services\Wellbeing\SupportPlanDirectory as SupportPlanDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SupportPlanDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(as: 'due', except: false)]
    public bool $dueOnly = false;

    protected SupportPlanDirectoryService $directory;

    public function boot(SupportPlanDirectoryService $directory): void
    {
        Gate::authorize('viewAny', SupportPlan::class);

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

    public function updatedDueOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->category = '';
        $this->dueOnly = false;
        $this->resetPage();
    }

    public function render(): View
    {
        $reader = auth()->user();
        abort_unless($reader !== null, 403);
        $selectedStatus = SupportPlanStatus::tryFrom($this->status);
        $selectedCategory = SupportCategory::tryFrom($this->category);

        return ViewFactory::make('livewire.support-plan-directory', [
            'plans' => $this->directory->plans($reader, $selectedStatus, $selectedCategory, $this->dueOnly),
            'statuses' => SupportPlanStatus::cases(),
            'categories' => SupportCategory::cases(),
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory,
            'openCount' => $this->directory->openCount($reader),
            'dueCount' => $this->directory->dueCount($reader),
        ]);
    }

    private function normalizeFilters(): void
    {
        $this->status = $this->normalizedStatus($this->status);
        $this->category = $this->normalizedCategory($this->category);
    }

    private function normalizedStatus(string $status): string
    {
        $selectedStatus = SupportPlanStatus::tryFrom($status);

        return $selectedStatus === null ? '' : $selectedStatus->value;
    }

    private function normalizedCategory(string $category): string
    {
        $selectedCategory = SupportCategory::tryFrom($category);

        return $selectedCategory === null ? '' : $selectedCategory->value;
    }
}
