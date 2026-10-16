<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Services\Gradebook\GradebookDirectory as GradebookDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GradebookDirectory extends Component
{
    use WithPagination;

    #[Url(as: 'academic_year_id')]
    public string $academicYearId = '';

    #[Url(as: 'academic_period_id')]
    public string $academicPeriodId = '';

    protected GradebookDirectoryService $directory;

    public function boot(GradebookDirectoryService $directory): void
    {
        Gate::authorize('viewAnyGradebooks', CourseOffering::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        if (!ctype_digit($this->academicYearId) || (int) $this->academicYearId < 1) {
            $this->academicYearId = (string) current_academic_year_id();
        }

        $academicYear = $this->selectedAcademicYear();

        if (!request()->query->has('academic_period_id') && $academicYear->id === current_academic_year_id()) {
            $this->academicPeriodId = (string) (current_academic_period_id() ?? '');
        }

        if (!ctype_digit($this->academicPeriodId)) {
            $this->academicPeriodId = '';
        }

        $this->selectedAcademicPeriod($academicYear);
    }

    public function updatedAcademicYearId(): void
    {
        if (!ctype_digit($this->academicYearId) || (int) $this->academicYearId < 1) {
            $this->academicYearId = (string) current_academic_year_id();
        }

        $academicYear = $this->selectedAcademicYear();
        $this->academicPeriodId = $academicYear->id === current_academic_year_id()
            ? (string) (current_academic_period_id() ?? '')
            : '';
        $this->resetPage();
    }

    public function updatedAcademicPeriodId(): void
    {
        if (!ctype_digit($this->academicPeriodId)) {
            $this->academicPeriodId = '';
        }

        $this->selectedAcademicPeriod($this->selectedAcademicYear());
        $this->resetPage();
    }

    public function render(): View
    {
        $academicYear = $this->selectedAcademicYear();
        $academicPeriod = $this->selectedAcademicPeriod($academicYear);
        $reader = auth()->user();
        abort_unless($reader !== null, 403);

        return ViewFactory::make('livewire.gradebook-directory', [
            'academicYear' => $academicYear,
            'academicYears' => $this->directory->academicYears(),
            'academicPeriod' => $academicPeriod,
            'courseOfferings' => $this->directory->courseOfferings($academicYear, $academicPeriod, $reader),
        ]);
    }

    private function selectedAcademicYear(): AcademicYear
    {
        if (!ctype_digit($this->academicYearId) || (int) $this->academicYearId < 1) {
            abort(404);
        }

        return $this->directory->academicYear((int) $this->academicYearId) ?? abort(404);
    }

    private function selectedAcademicPeriod(AcademicYear $academicYear): ?AcademicPeriod
    {
        if (!ctype_digit($this->academicPeriodId) || (int) $this->academicPeriodId < 1) {
            return null;
        }

        return $this->directory->academicPeriod($academicYear, (int) $this->academicPeriodId) ?? abort(404);
    }
}
