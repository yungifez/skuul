<?php

namespace App\Livewire;

use App\Enums\SyllabusStatus;
use App\Models\AcademicPeriod;
use App\Models\Syllabus;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Show every class's progress through its published syllabus, most behind first.
 */
class SyllabusCoverageReport extends Component
{
    #[Url(as: 'period')]
    public string $academicPeriodId = '';

    #[Url(as: 'behind')]
    public bool $onlyBehind = false;

    public function mount(): void
    {
        Gate::authorize('viewCoverage', Syllabus::class);

        if ($this->academicPeriodId === '') {
            $this->academicPeriodId = (string) (academic_period_context()->academicPeriodId() ?? $this->periods()->last()->id ?? '');
        }
    }

    public function render(SyllabusCoverageService $coverage): View
    {
        Gate::authorize('viewCoverage', Syllabus::class);

        $rows = [];

        foreach ($this->publishedSyllabi() as $syllabus) {
            foreach ($coverage->summary($syllabus) as $track) {
                if ($this->onlyBehind && $track['behind'] === 0) {
                    continue;
                }

                $rows[] = [
                    ...$track,
                    'syllabus' => $syllabus,
                    'class' => $track['id'] === null ? $syllabus->courseOffering->academicLevel->name : $track['label'],
                ];
            }
        }

        usort($rows, fn (array $first, array $second): int => [$second['behind'], $first['percent']] <=> [$first['behind'], $second['percent']]);

        return view('livewire.syllabus-coverage-report', [
            'rows' => collect($rows),
            'periods' => $this->periods(),
        ]);
    }

    /**
     * Get the published syllabi of the chosen period.
     *
     * @return EloquentCollection<int, Syllabus>
     */
    private function publishedSyllabi(): EloquentCollection
    {
        if ($this->academicPeriodId === '') {
            return new EloquentCollection;
        }

        return Syllabus::query()
            ->inSchool()
            ->where('status', SyllabusStatus::Published)
            ->whereHas('courseOffering', fn (Builder $offering): Builder => $offering->where('academic_period_id', (int) $this->academicPeriodId))
            ->with(['courseOffering.subject:id,name', 'courseOffering.academicLevel', 'courseOffering.academicPeriod'])
            ->get();
    }

    /**
     * @return EloquentCollection<int, AcademicPeriod>
     */
    private function periods(): EloquentCollection
    {
        return AcademicPeriod::query()
            ->inSchool()
            ->whereHas('academicYear')
            ->with('academicYear:id,start_year,stop_year')
            ->orderBy('academic_year_id')
            ->orderBy('position')
            ->get(['id', 'name', 'label', 'academic_year_id', 'position']);
    }
}
