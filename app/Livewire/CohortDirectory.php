<?php

namespace App\Livewire;

use App\Enums\CohortType;
use App\Models\Cohort;
use App\Services\Cohorts\CohortDirectory as CohortDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CohortDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $type = '';

    #[Url(as: 'active', except: false)]
    public bool $activeOnly = false;

    protected CohortDirectoryService $directory;

    public function boot(CohortDirectoryService $directory): void
    {
        Gate::authorize('viewAny', Cohort::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        if (CohortType::tryFrom($this->type) === null) {
            $this->type = '';
        }
    }

    public function updatedType(): void
    {
        if (CohortType::tryFrom($this->type) === null) {
            $this->type = '';
        }

        $this->resetPage();
    }

    public function updatedActiveOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->type = '';
        $this->activeOnly = false;
        $this->resetPage();
    }

    public function render(): View
    {
        $selectedType = CohortType::tryFrom($this->type);

        return ViewFactory::make('livewire.cohort-directory', [
            'cohorts' => $this->directory->groups(
                type: $selectedType,
                activeOnly: $this->activeOnly,
                mayReadRestricted: auth()->user()?->can('read restricted cohort') === true,
            ),
            'types' => CohortType::cases(),
            'selectedType' => $selectedType,
        ]);
    }
}
