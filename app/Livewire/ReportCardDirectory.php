<?php

namespace App\Livewire;

use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\ReportCardSnapshot;
use App\Services\Report\ReportCardDirectory as ReportCardDirectoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View as ViewFactory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReportCardDirectory extends Component
{
    use WithPagination;

    #[Url(as: 'student_record_id', except: '')]
    public string $studentRecordId = '';

    #[Url(as: 'academic_year_id', except: '')]
    public string $academicYearId = '';

    #[Url(as: 'academic_period_id', except: '')]
    public string $academicPeriodId = '';

    protected ReportCardDirectoryService $directory;

    public function boot(ReportCardDirectoryService $directory): void
    {
        Gate::authorize('viewAny', ReportCardSnapshot::class);

        $this->directory = $directory;
    }

    public function mount(): void
    {
        $this->normalizeFilters();
        $this->selectedAcademicPeriod($this->selectedAcademicYear());
    }

    public function updatedStudentRecordId(): void
    {
        $this->studentRecordId = $this->normalizeId($this->studentRecordId);
        $this->resetPage();
    }

    public function updatedAcademicYearId(): void
    {
        $this->academicYearId = $this->normalizeId($this->academicYearId);
        $this->academicPeriodId = '';
        $this->resetPage();
    }

    public function updatedAcademicPeriodId(): void
    {
        $this->academicPeriodId = $this->normalizeId($this->academicPeriodId);
        $this->selectedAcademicPeriod($this->selectedAcademicYear());
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->studentRecordId = '';
        $this->academicYearId = '';
        $this->academicPeriodId = '';
        $this->resetPage();
    }

    public function render(): View
    {
        $academicYear = $this->selectedAcademicYear();
        $academicPeriod = $this->selectedAcademicPeriod($academicYear);

        return ViewFactory::make('livewire.report-card-directory', [
            'students' => $this->directory->students(),
            'academicYears' => $this->directory->academicYears(),
            'periods' => $this->directory->periods($academicYear),
            'reportCards' => $this->directory->reportCards(
                $this->studentRecordId === '' ? null : (int) $this->studentRecordId,
                $academicYear,
                $academicPeriod,
            ),
            'selectedStudent' => $this->studentRecordId === '' ? null : (int) $this->studentRecordId,
            'selectedAcademicYear' => $academicYear?->id,
            'selectedPeriod' => $academicPeriod?->id,
        ]);
    }

    private function normalizeFilters(): void
    {
        $this->studentRecordId = $this->normalizeId($this->studentRecordId);
        $this->academicYearId = $this->normalizeId($this->academicYearId);
        $this->academicPeriodId = $this->normalizeId($this->academicPeriodId);
    }

    private function normalizeId(string $value): string
    {
        return ctype_digit($value) && (int) $value > 0 ? $value : '';
    }

    private function selectedAcademicYear(): ?AcademicYear
    {
        if ($this->academicYearId === '') {
            return null;
        }

        return $this->directory->academicYear((int) $this->academicYearId) ?? abort(404);
    }

    private function selectedAcademicPeriod(?AcademicYear $academicYear): ?AcademicPeriod
    {
        if ($this->academicPeriodId === '') {
            return null;
        }

        $academicPeriod = $this->directory->academicPeriod((int) $this->academicPeriodId) ?? abort(404);

        abort_if($academicYear !== null && $academicPeriod->academic_year_id !== $academicYear->id, 404);

        return $academicPeriod;
    }
}
