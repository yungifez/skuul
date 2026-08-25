<?php

namespace App\Livewire;

use App\Livewire\Concerns\InteractsWithAprilTable;
use App\Models\AcademicYear;
use App\Models\Promotion;
use App\Services\AcademicYear\AcademicYearService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Yungifez\AprilUI\Livewire\Columns\Column;
use Yungifez\AprilUI\Livewire\DataTableComponent;

class ListPromotionsTable extends DataTableComponent
{
    use InteractsWithAprilTable;

    public AcademicYear|int|null $academicYear = null;

    public function mount(?AcademicYearService $academicYearService = null): void
    {
        parent::mount();

        if (!$this->academicYear) {
            $this->academicYear = current_school()->load('academicYear')->academicYear->first();
        } elseif (is_int($this->academicYear)) {
            $this->academicYear = ($academicYearService ?? app(AcademicYearService::class))->getAcademicYearById($this->academicYear);
        }
    }

    protected function builder(): Builder
    {
        return Promotion::query()->where('academic_year_id', $this->academicYear?->id)->with(['sourceAcademicCycleSection.academicLevel', 'destinationAcademicCycleSection.academicLevel']);
    }

    /** @return array<int, Column> */
    protected function columns(): array
    {
        return [Column::make('From level', 'from_level'), Column::make('From section', 'from_section'), Column::make('To level', 'to_level'), Column::make('To section', 'to_section'), Column::make('Learners', 'learners_count')->sortable()];
    }

    /** @return array<int, array<string, mixed>> */
    protected function serializeRows(Collection $rows): array
    {
        return $rows->map(function (Promotion $promotion): array {
            $row = $promotion->toArray();
            $row['from_level'] = $promotion->sourceAcademicCycleSection->academicLevel->name;
            $row['from_section'] = $promotion->sourceAcademicCycleSection->name;
            $row['to_level'] = $promotion->destinationAcademicCycleSection->academicLevel->name;
            $row['to_section'] = $promotion->destinationAcademicCycleSection->name;
            $row['learners_count'] = count($promotion->students ?? []);
            $row['view_url'] = route('students.promotions.show', $promotion);
            $row['reset_url'] = route('students.promotions.reset', $promotion);

            return $row;
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.list-promotions-table', array_merge($this->aprilTablePayload(), [
            'canViewPromotions' => auth()->user()->can('read promotion'),
            'canResetPromotions' => auth()->user()->can('reset promotion'),
        ]));
    }
}
