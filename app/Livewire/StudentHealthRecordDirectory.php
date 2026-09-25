<?php

namespace App\Livewire;

use App\Models\StudentHealthRecord;
use App\Services\Wellbeing\StudentHealthRecordDirectory as StudentHealthRecordDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StudentHealthRecordDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(as: 'missing', except: false)]
    public bool $missingOnly = false;

    protected StudentHealthRecordDirectoryService $directory;

    public function boot(StudentHealthRecordDirectoryService $directory): void
    {
        Gate::authorize('viewAny', StudentHealthRecord::class);

        $this->directory = $directory;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedMissingOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->missingOnly = false;
        $this->resetPage();
    }

    public function render(): View
    {
        return ViewFactory::make('livewire.student-health-record-directory', [
            'learners' => $this->directory->learners($this->search, $this->missingOnly),
            'recordedCount' => $this->directory->recordedCount(),
            'learnerCount' => $this->directory->learnerCount(),
        ]);
    }
}
