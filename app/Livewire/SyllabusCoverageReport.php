<?php

namespace App\Livewire;

use App\Enums\SyllabusStatus;
use App\Models\AcademicPeriod;
use App\Models\Syllabus;
use App\Services\Report\Formats\CsvFormat;
use App\Services\Syllabus\SyllabusCoverageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return view('livewire.syllabus-coverage-report', [
            'rows' => collect($this->rows($coverage)),
            'periods' => $this->periods(),
        ]);
    }

    /**
     * Download the rows on screen as a spreadsheet file.
     */
    public function export(SyllabusCoverageService $coverage, CsvFormat $csv): StreamedResponse
    {
        Gate::authorize('viewCoverage', Syllabus::class);

        $period = $this->periods()->firstWhere('id', (int) $this->academicPeriodId);
        $rows = collect($this->rows($coverage))->map($this->csvRow(...));
        $content = $csv->render('Syllabus coverage', ['Subject', 'Class', 'Syllabus', 'Revision', 'Topics', 'Covered', 'Partly covered', 'Skipped', 'Behind plan', 'Percent covered'], $rows);
        $name = 'syllabus-coverage-'.Str::slug($period === null ? 'period' : ($period->label ?? $period->name)).'.csv';

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $name, ['Content-Type' => $csv->mimeType()]);
    }

    /**
     * Get one row for each class of each published syllabus, most behind first.
     *
     * @return list<array{id: int|null, label: string, total: int, covered: int, partial: int, skipped: int, expected: int, behind: int, percent: int, syllabus: Syllabus, class: string}>
     */
    private function rows(SyllabusCoverageService $coverage): array
    {
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

        return $rows;
    }

    /**
     * Turn one report row into the cells of the spreadsheet.
     *
     * @param  array{total: int, covered: int, partial: int, skipped: int, behind: int, percent: int, syllabus: Syllabus, class: string}  $row
     * @return array<int, mixed>
     */
    private function csvRow(array $row): array
    {
        return array_map($this->plainCell(...), [
            $row['syllabus']->courseOffering->subject->name,
            $row['class'],
            $row['syllabus']->name,
            $row['syllabus']->revision,
            $row['total'],
            $row['covered'],
            $row['partial'],
            $row['skipped'],
            $row['behind'],
            $row['percent'],
        ]);
    }

    /**
     * Keep a spreadsheet from reading a name as a formula.
     */
    private function plainCell(int|string $value): int|string
    {
        return is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) === 1 ? "'".$value : $value;
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
