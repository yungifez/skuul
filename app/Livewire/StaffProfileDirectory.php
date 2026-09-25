<?php

namespace App\Livewire;

use App\Enums\StaffStatus;
use App\Models\StaffProfile;
use App\Services\Staff\StaffProfileDirectory as StaffProfileDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StaffProfileDirectory extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(as: 'away', except: false)]
    public bool $awayOnly = false;

    protected StaffProfileDirectoryService $directory;

    public function mount(): void
    {
        $selectedStatus = StaffStatus::tryFrom($this->status);

        if ($selectedStatus === null) {
            $this->status = '';
        }
    }

    public function boot(StaffProfileDirectoryService $directory): void
    {
        Gate::authorize('viewAny', StaffProfile::class);
        $this->directory = $directory;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedAwayOnly(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->awayOnly = false;
        $this->resetPage();
    }

    public function render(): View
    {
        $selectedStatus = StaffStatus::tryFrom($this->status);

        return ViewFactory::make('livewire.staff-profile-directory', [
            'profiles' => $this->directory->profiles($this->search, $this->status, $this->awayOnly),
            'statuses' => StaffStatus::cases(),
            'selectedStatus' => $selectedStatus,
            'employedCount' => $this->directory->employedCount(),
            'awayCount' => $this->directory->awayCount(),
        ]);
    }
}
