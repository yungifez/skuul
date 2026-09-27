<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The sections of each school year, filtered by year, class, and status.
 */
class SectionDirectory extends Component
{
    use WithPagination;

    #[Url(as: 'academic_year_id')]
    public string $academicYearId = '';

    #[Url(as: 'academic_level_id')]
    public string $academicLevelId = '';

    #[Url]
    public string $status = '';

    /**
     * Open on the year being worked in. An explicit empty year asks for every year.
     */
    public function mount(): void
    {
        Gate::authorize('viewAny', AcademicCycleSection::class);

        if (!request()->has('academic_year_id') && $this->academicYearId === '') {
            $this->academicYearId = (string) (current_academic_year_id() ?? '');
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['academicYearId', 'academicLevelId', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset('academicYearId', 'academicLevelId', 'status');
        $this->resetPage();
    }

    public function render(): View
    {
        $academicYears = AcademicYear::inSchool()->orderByDesc('start_year')->orderByDesc('id')->get(['id', 'start_year', 'stop_year', 'status']);
        $academicLevels = AcademicLevel::inSchool()->where('is_group', false)->orderBy('position')->orderBy('name')->get(['id', 'name']);

        $academicYearId = $this->chosenId($this->academicYearId, $academicYears->modelKeys());
        $academicLevelId = $this->chosenId($this->academicLevelId, $academicLevels->modelKeys());
        $status = AcademicStructureStatus::tryFrom($this->status);

        return view('livewire.section-directory', [
            'academicCycleSections' => AcademicCycleSection::inSchool()
                ->when($academicYearId !== null, fn (Builder $query): Builder => $query->where('academic_year_id', $academicYearId))
                ->when($academicLevelId !== null, fn (Builder $query): Builder => $query->where('academic_level_id', $academicLevelId))
                ->when($status !== null, fn (Builder $query): Builder => $query->where('status', $status))
                ->with(['academicLevel:id,name', 'academicYear:id,start_year,stop_year,status', 'homeroomTeacher:id,name'])
                ->orderByDesc('academic_year_id')
                ->orderBy('academic_level_id')
                ->orderBy('position')
                ->orderBy('name')
                ->paginate(25),
            'academicYears' => $academicYears,
            'academicLevels' => $academicLevels,
            'selectedYear' => $academicYears->firstWhere('id', $academicYearId),
            'isFiltered' => $academicYearId !== null || $academicLevelId !== null || $status !== null,
            'totalCount' => AcademicCycleSection::inSchool()->count(),
        ]);
    }

    /**
     * @param  array<int, int>  $allowed
     */
    private function chosenId(string $value, array $allowed): ?int
    {
        return ctype_digit($value) && in_array((int) $value, $allowed, true) ? (int) $value : null;
    }
}
