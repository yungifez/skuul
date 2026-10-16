<?php

namespace App\Livewire;

use App\Enums\ProgramType;
use App\Models\Program;
use App\Services\Cohorts\ProgramDirectory as ProgramDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $type = '';

    #[Url(as: 'active', except: false)]
    public bool $activeOnly = false;

    protected ProgramDirectoryService $directory;

    public function boot(ProgramDirectoryService $directory): void
    {
        Gate::authorize('viewAny', Program::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        if (ProgramType::tryFrom($this->type) === null) {
            $this->type = '';
        }
    }

    public function updatedType(): void
    {
        if (ProgramType::tryFrom($this->type) === null) {
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
        $selectedType = ProgramType::tryFrom($this->type);

        return ViewFactory::make('livewire.program-directory', [
            'programs' => $this->directory->programs($selectedType, $this->activeOnly),
            'types' => ProgramType::cases(),
            'selectedType' => $selectedType,
        ]);
    }
}
